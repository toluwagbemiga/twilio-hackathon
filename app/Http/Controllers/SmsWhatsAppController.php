<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use LanguageDetector\LanguageDetector;
use App\Models\Contact;
use App\Models\SmsHistory;
use Illuminate\Support\Facades\Log;
use Exception;
use Gemini\Laravel\Facades\Gemini;

class SmsWhatsAppController extends Controller
{
    protected $twilio;
    protected $openAI;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        
    }

    public function handleIncomingMessage(Request $request)
    {
        $messageBody = $request->input('Body');
        $userPhone = $request->input('From');
        $messageType = strpos($userPhone, 'whatsapp:') !== false ? 'whatsapp' : 'sms';
        $user = User::firstOrCreate(['phone' => $userPhone]);

        // Detect language
        $detector = new LanguageDetector();
        $languageCode = $detector->evaluate($messageBody)->getLanguage();
        $user->language = $languageCode;
        $user->save();

        // Retrieve or create a conversation
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id, 'is_closed' => false, 'type' => $messageType]
        );

        // Save user message
        $conversation->messages()->create([
            'sender' => 'user',
            'content' => $messageBody
        ]);

        // Check if the conversation is taken over by an agent
        if ($conversation->is_agent) {
            return response()->json(['status' => 'success']);
        }

        // Process AI response
        $responseMessage = $this->processAIResponse($user, $messageBody, $conversation);

        // Save AI message
        $conversation->messages()->create([
            'sender' => 'ai',
            'content' => $responseMessage
        ]);

        // Send response via Twilio
        $this->sendTwilioResponse($userPhone, $responseMessage, $messageType);

        return response()->json(['status' => 'success']);
    }

    public function sendBulkSMS(Request $request)
{
    $request->validate([
        'sms_content' => 'required|string',
        'method' => 'required|string|in:sms,whatsapp',
        'recipients' => 'required|array',
    ]);

    $smsContent = $request->input('sms_content');
    $recipientIds = $request->input('recipients');
    $method = $request->input('method');
    $recipients = Contact::whereIn('id', $recipientIds)->get();
    $recipientData = [];

    foreach ($recipients as $recipient) {
        try {
            $personalizedMessage = str_replace('{name}', $recipient->name, $smsContent);
            $to = $method === 'whatsapp' ? "whatsapp:{$recipient->phone_number}" : $recipient->phone_number;
            $this->twilio->messages->create($to, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $personalizedMessage,
            ]);
            $recipientData[] = ['name' => $recipient->name, 'phone_number' => $recipient->phone_number];

            Log::info('Successfully sent SMS to ' . $recipient->name . ' (' . $recipient->phone_number . ')');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send message to ' . $recipient->name, 'error' => $e->getMessage()], 500);
        }
    }
    Log::info('Saving SMS history.');
    SmsHistory::create([
        'sms_content' => $smsContent,
        'recipients' => json_encode($recipientData),
    ]);
    Log::info('Bulk SMS sending process completed.');
    return response()->json(['message' => 'Bulk messages sent successfully']);
}

    public function showGenerateSMSForm()
    {
        $contacts = Contact::all();
        return view('chat.generate_sms', compact('contacts'));
    }
    
    public function generateSMS(Request $request)
    {
        // Validate request data
        $request->validate([
            'prompt' => 'required|string',
        ]);
    
        // Log validation success
        Log::debug('Request validation successful.');
    
        try {
            // Initialize OpenAI client
            Log::debug('Initializing OpenAI client...');
            
    
            // Create OpenAI request
            //Log::debug('Creating OpenAI request with prompt:', $request->input('prompt'));
          
            $response = Gemini::geminiPro()->generateContent($request->input('prompt'));

            // Extract generated message
            Log::debug('Extracting generated message from response...');
            $generatedMessage = $response->text();
            // Log success and return response
            Log::info('SMS generation successful. Returning response.');
            return response()->json([
                'message' => $generatedMessage,
            ]);
    
        } catch (Exception $e) {
            // Log error message
            Log::error('Error generating SMS: ' . $e->getMessage());
            report($e); // Optional: Report the exception for further analysis
    
            // Return error response
            return response()->json([
                'error' => 'An error occurred while generating SMS.',
            ], 500);
        }
    }
    
    private function processAIResponse(User $user, $messageBody, $conversation)
    {
        // Include previous messages in the context
        $context = '';
        foreach ($conversation->messages as $message) {
            $context .= ($message->sender === 'user' ? 'User: ' : 'AI: ') . $message->content . "\n";
        }

        // Add the latest message from the user
        $context .= 'User: ' . $messageBody . "\n";

        // Handle special request to talk to an agent
        if (strtolower($messageBody) === 'talk to an agent') {
            $conversation->is_agent = true;
            $conversation->save();
            return 'You will be connected to a human agent shortly.';
        }

        // Call your AI API here
        
        
    
        $response = Gemini::geminiPro()->generateContent($context);

        // Extract generated message
        Log::debug('Extracting generated message from response...');
        return $response->text();
        // Log success and return response
      

    }

    private function sendTwilioResponse($to, $message, $type)
    {
        $from = $type === 'whatsapp' ? 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER') : env('TWILIO_PHONE_NUMBER');

        $this->twilio->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);
    }
}
