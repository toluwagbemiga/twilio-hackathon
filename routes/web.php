
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactCommunicationController;
use App\Http\Controllers\SmsWhatsAppController;
use App\Http\Controllers\VoiceController;
use Illuminate\Support\Facades\Route;

// Admin Dashboard
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/conversations/{id}/close', [AdminController::class, 'closeConversation'])->name('admin.conversation.close');

// Contacts and Communications
Route::resource('contacts', ContactCommunicationController::class);
Route::get('/conversations', [ContactCommunicationController::class, 'indexConversations'])->name('conversations.index');
Route::get('/conversations/{conversation}', [ContactCommunicationController::class, 'showConversation'])->name('conversations.show');
Route::post('/conversations/{conversation}/respond', [ContactCommunicationController::class, 'respondToConversation'])->name('conversations.respond');
Route::post('/conversations/{conversation}/close', [ContactCommunicationController::class, 'closeConversation'])->name('conversations.close');

// Bulk SMS

Route::get('/chat/generate-sms', [SmsWhatsAppController::class, 'showGenerateSMSForm'])->name('chat.showGenerateSMSForm');
Route::post('/chat/generate-sms', [SmsWhatsAppController::class, 'generateSMS'])->name('chat.generateSMS');
Route::post('/chat/send-bulk-sms', [SmsWhatsAppController::class, 'sendBulkSMS'])->name('chat.sendBulkSMS');

// SMS and WhatsApp Webhooks
Route::post('/webhook/sms-whatsapp', [SmsWhatsAppController::class, 'handleIncomingMessage'])->name('webhook.smsWhatsapp');

// Voice Webhooks
Route::post('/webhook/voice/incoming', [VoiceController::class, 'handleIncomingCall'])->name('webhook.voice.incoming');
Route::post('/webhook/voice/keypad', [VoiceController::class, 'handleKeypadInput'])->name('webhook.voice.keypad');
Route::post('/webhook/voice/recording', [VoiceController::class, 'handleRecording'])->name('webhook.voice.recording');
Route::post('/webhook/voice/transcription', [VoiceController::class, 'handleTranscription'])->name('webhook.voice.transcription');
