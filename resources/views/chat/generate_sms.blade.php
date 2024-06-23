@extends('layouts.app')

@section('title', 'Generate Bulk SMS/WhatsApp')

@section('content')
<div class="container">
    <h1 class="my-4">Generate Bulk SMS/WhatsApp</h1>
    <form id="generateForm">
        @csrf
        <div class="mb-3">
            <label for="prompt" class="form-label">AI Prompt</label>
            <textarea name="prompt" class="form-control" id="prompt" required></textarea>
        </div>
        <button type="button" class="btn btn-primary" onclick="generateMessage()" >Generate Message</button>
    </form>
<p id="errorMessage"></p>
<div class="container mt-4" id="generatedMessageContainer" style="display: none;">
    <h2>Generated Message</h2>
    <div class="mb-3">
    </div>
    <form id="sendForm">
        @csrf
        <textarea class="form-control" id="sms_content" name="sms_content" rows="9" placeholder="Generated message will appear here..."></textarea>
        
        <div class="mb-3">
            <label for="method" class="form-label">Method</label>
            <select name="method" id="method" class="form-select" required>
                <option value="sms">SMS</option>
                <option value="whatsapp">WhatsApp</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="recipients" class="form-label">Recipients</label>
            <div id="recipients" class="form-check">
                @foreach ($contacts as $contact)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="recipients[]" value="{{ $contact->id }}" id="contact-{{ $contact->id }}">
                        <label class="form-check-label" for="contact-{{ $contact->id }}">
                            {{ $contact->name }} ({{ $contact->phone_number }})
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        <button type="button" class="btn btn-success" onclick="sendBulkMessage()">Send Bulk Message</button>
    </form>
</div>

</div>

<script>
    function generateMessage() {
        let formData = new FormData(document.getElementById('generateForm'));
        
        fetch("{{ route('chat.generateSMS') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('sms_content').value = data.message;
            document.getElementById('generatedMessageContainer').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            //  Extract and display a user-friendly error message
            let errorMessage = 'An unknown error occurred.';
            if (error.response && error.response.data) {
                errorMessage = error.response.data.error || errorMessage; // Use error message from response if available
            }
            document.getElementById('errorMessage').innerText = errorMessage; // Display error message in an element
            });
    }

    function sendBulkMessage() {
        let formData = new FormData(document.getElementById('sendForm'));

        fetch("{{ route('chat.sendBulkSMS') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData
        })
        .then(response =>response.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
                window.location.href = "{{ route('admin.dashboard') }}";
            } else {
                response.json().then(data => alert(data.message));
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection
