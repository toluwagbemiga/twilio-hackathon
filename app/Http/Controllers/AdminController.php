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
     
    }

    public function index()
    {
        $callConversations = Conversation::with('user', 'messages')
            ->where('type', 'call')
            ->get();
        $smsConversations = Conversation::with('user', 'messages')
            ->where('type', 'sms')
            ->get();
        $WhatsappConversations = Conversation::with('user', 'messages')
            ->where('type', 'whatsapp')
            ->get();
        return view('admin.dashboard', compact('callConversations', 'smsConversations', 'WhatsappConversations'));
    }

    public function closeConversation($id)
    {
        $conversation = Conversation::find($id);
        $conversation->is_closed = true;
        $conversation->save();

        return redirect()->back()->with('status', 'Conversation closed.');
    }

   
}
