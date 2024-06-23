<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use LanguageDetector\LanguageDetector;
use Gemini\Laravel\Facades\Gemini;

class VoiceController extends Controller
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function handleIncomingCall(Request $request)
    {
        $userPhone = $request->input('From');
        $user = User::firstOrCreate(['phone' => $userPhone]);

        $response = new VoiceResponse();
        $response->gather([
            'input' => 'dtmf',
            'numDigits' => 1,
            'action' => route('webhook.voice.keypad'),
            'method' => 'POST'
        ])->say('Press 0 to talk to an agent or any other key to continue.', ['language' => $user->language]);

        return response($response)->header('Content-Type', 'text/xml');
    }

    public function handleKeypadInput(Request $request)
    {
        $digits = $request->input('Digits');
        $userPhone = $request->input('From');
        $user = User::where('phone', $userPhone)->first();

        $response = new VoiceResponse();

        if ($digits == '0') {
            $conversation = Conversation::where('user_id', $user->id)->where('is_closed', false)->where('type', 'call')->first();
            if ($conversation) {
                $conversation->is_agent = true;
                $conversation->save();
            }
            $response->say('Connecting you to an agent.', ['language' => $user->language]);
            $response->dial(env('AGENT_PHONE_NUMBER'));
        } else {
            $response->say('Please state your query after the beep.', ['language' => $user->language]);
            $response->record([
                'action' => route('webhook.voice.recording'), 
                'maxLength' => 30, 
                'transcribe' => true, 
                'transcribeCallback' => route('webhook.voice.transcription'),
                'playBeep' => true
           ]);
        }

        return response($response)->header('Content-Type', 'text/xml');
    }

    public function handleRecording(Request $request)
    {
        $recordingUrl = $request->input('RecordingUrl');
        $userPhone = $request->input('From');
        $user = User::where('phone', $userPhone)->first();

        // Retrieve or create a conversation
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id, 'is_closed' => false, 'type' => 'call']
        );

        $conversation->messages()->create([
            'sender' => 'user',
            'content' => $recordingUrl
        ]);

        $response = new VoiceResponse();
        $response->say('Your message has been recorded. We will get back to you shortly.', ['language' => $user->language]);

        return response($response)->header('Content-Type', 'text/xml');
    }

    public function handleTranscription(Request $request)
    {
        $transcriptionText = $request->input('TranscriptionText');
        $userPhone = $request->input('From');
        $user = User::firstOrCreate(['phone' => $userPhone]);

        // Check if language is already set for the user
        if (empty($user->language)) {
            $detector = new LanguageDetector();
            $languageCode = $detector->evaluate($transcriptionText)->getLanguage();
            $user->language = $languageCode;
            $user->save();
        }
        // Now $user contains the user object, with language updated only if it was empty

        $languageCode = $user->language;

        // Retrieve the conversation
        $conversation = Conversation::where('user_id', $user->id)->where('is_closed', false)->where('type', 'call')->first();

        // Check if the conversation is taken over by an agent
        if ($conversation->is_agent) {
            return response()->json(['status' => 'success']);
        }

        // Process AI response
        $responseMessage = $this->processAIResponse($user, $transcriptionText, $conversation);

        // Save messages
        $conversation->messages()->create([
            'sender' => 'user',
            'content' => $transcriptionText
        ]);
        $conversation->messages()->create([
            'sender' => 'ai',
            'content' => $responseMessage
        ]);

        // Send AI response via Twilio
        $this->sendTwilioVoiceResponse($userPhone, $responseMessage, $languageCode);

        return response()->json(['status' => 'success']);
    }

    private function processAIResponse(User $user, $transcriptionText, $conversation)
    {
        // Include previous messages in the context
        $context = '';
        foreach ($conversation->messages as $message) {
            $context .= ($message->sender === 'user' ? 'User: ' : 'AI: ') . $message->content . "\n";
        }

        // Add the latest message from the user
        $context .= 'User: ' . $transcriptionText . "\n";

        // Handle special request to talk to an agent
        if (strtolower($transcriptionText) === 'talk to an agent') {
            $conversation->is_agent = true;
            $conversation->save();
            return 'You will be connected to a human agent shortly.';
        }

        // Call your AI API here
        $response = Gemini::geminiPro()->generateContent($context);
        return $response->text();
    }

    private function sendTwilioVoiceResponse($to, $message, $language)
    {
        $response = new VoiceResponse();
        $response->say($message, ['language' => $language]);

        $this->twilio->calls->create($to, env('TWILIO_PHONE_NUMBER'), [
            'twiml' => $response
        ]);
    }
}
