<?php

// app/Http/Controllers/ContactCommunicationController.php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use OpenAI\Client as OpenAIClient;

class ContactCommunicationController extends Controller
{
    protected $openAiClient;
    protected $twilioClient;

    public function __construct()
    {
        
        $this->twilioClient = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function index()
    {
        $contacts = Contact::all();
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required|unique:contacts',
        ]);

        Contact::create($request->all());

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required|unique:contacts,phone_number,' . $contact->id,
        ]);

        $contact->update($request->all());

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function indexConversations()
    {
        $conversations = Conversation::where('is_closed', false)->get();
        return view('conversations.index', compact('conversations'));
    }

    public function showConversation(Conversation $conversation)
    {
        return view('conversations.show', compact('conversation'));
    }

    public function respondToConversation(Request $request, Conversation $conversation)
    {
        $adminMessage = $request->input('message');

        // Add admin message to conversation
        $conversation->messages()->create([
            'sender' => 'admin',
            'content' => $adminMessage,
        ]);

        // Send admin message via Twilio
        $this->sendTwilioMessage($conversation->user->phone, $adminMessage, $conversation->type);

        return redirect()->route('conversations.show', $conversation->id);
    }

    public function closeConversation(Conversation $conversation)
    {
        $conversation->is_closed = true;
        $conversation->save();

        return redirect()->route('conversations.index');
    }

    private function sendTwilioMessage($to, $message, $type)
    {
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $from = $type === 'whatsapp' ? 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER') : env('TWILIO_PHONE_NUMBER');

        $twilio->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);
    }
    
}
