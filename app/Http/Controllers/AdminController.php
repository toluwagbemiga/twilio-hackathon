<?php

// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\BulkSMS;
use Illuminate\Http\Request;
use OpenAI\Client as OpenAIClient;

class AdminController extends Controller
{
    protected $openAiClient;

    public function __construct()
    {
        $this->openAiClient = new OpenAIClient(env('OPENAI_API_KEY'));
    }

    public function indexDashboard()
    {
        $callConversations = Conversation::with('user', 'messages')
            ->where('type', 'call')
            ->get();
        $smsConversations = Conversation::with('user', 'messages')
            ->where('type', 'sms')
            ->get();

        return view('admin.dashboard', compact('callConversations', 'smsConversations'));
    }

    public function closeDashboardConversation($id)
    {
        $conversation = Conversation::find($id);
        $conversation->is_closed = true;
        $conversation->save();

        return redirect()->back()->with('status', 'Conversation closed.');
    }

    public function generateSMS(Request $request)
    {
        $description = $request->input('description');
        $maxTokens = $request->input('max_tokens');

        $response = $this->openAiClient->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $description,
            'max_tokens' => $maxTokens,
        ]);

        $generatedSMS = trim($response['choices'][0]['text']);

        // Save the BulkSMS record
        BulkSMS::create([
            'description' => $description,
            'generated_sms' => $generatedSMS,
            'max_tokens' => $maxTokens
        ]);

        return response()->json(['generated_sms' => $generatedSMS]);
    }
}
