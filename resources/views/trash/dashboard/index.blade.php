<!-- resources/views/dashboard/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Customer Support Dashboard</title>
    <style>/* Add this in a CSS file or within a <style> tag in the view */

        .notification {
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            color: white;
            text-align: center;
        }
        
        .notification.success {
            background-color: green;
        }
        
        .notification.error {
            background-color: red;
        }
        </style>
</head>
<body>
    <h1>Support Dashboard</h1>
    <div id="dashboard-container">
        <!-- Other sections of the dashboard -->
        <!-- resources/views/dashboard/index.blade.php -->

<!-- Add this somewhere in the body -->
<div id="notifications"></div>

<script>
function generateSMS() {
    const description = document.getElementById('description').value;
    const maxTokens = document.getElementById('max_tokens').value;
    
    fetch('/generate-sms', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ description: description, max_tokens: maxTokens })
    })
    .then(response => response.json())
    .then(data => {
        if (data.generated_sms) {
            document.getElementById('generated-sms').innerText = data.generated_sms;
            document.getElementById('generated-sms-container').style.display = 'block';
            showNotification('SMS generated successfully', 'success');
        } else {
            showNotification('Failed to generate SMS', 'error');
        }
    });
}

function sendBulkSMS() {
    const generatedSMS = document.getElementById('generated-sms').innerText;
    const selectedRecipients = Array.from(document.querySelectorAll('input[name="recipients[]"]:checked')).map(cb => cb.value);
    
    fetch('/send-bulk-sms', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ sms_content: generatedSMS, recipients: selectedRecipients })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            showNotification(data.message, 'success');
        } else if (data.error) {
            showNotification(data.error, 'error');
        }
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerText = message;
    document.getElementById('notifications').appendChild(notification);
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

        <h2>Bulk SMS</h2>
        <form id="bulk-sms-form">
            @csrf
            <label for="description">Brief Description:</label>
            <textarea id="description" name="description" required></textarea>
            <label for="max_tokens">Max Tokens:</label>
            <input type="number" id="max_tokens" name="max_tokens" required>
            <h3>Contacts</h3>
            @foreach ($contacts as $contact)
                <div>
                    <input type="checkbox" name="recipients[]" value="{{ $contact->id }}">
                    <label>{{ $contact->name }} ({{ $contact->phone_number }})</label>
                </div>
            @endforeach
            <button type="button" onclick="generateSMS()">Generate SMS</button>
        </form>
        <div id="generated-sms-container" style="display: none;">
            <h3>Generated SMS</h3>
            <p id="generated-sms"></p>
            <button type="button" onclick="sendBulkSMS()">Send Bulk SMS</button>
        </div>
    </div>

    <script>
        function generateSMS() {
            const description = document.getElementById('description').value;
            const maxTokens = document.getElementById('max_tokens').value;
            
            fetch('/generate-sms', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ description: description, max_tokens: maxTokens })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('generated-sms').innerText = data.generated_sms;
                document.getElementById('generated-sms-container').style.display = 'block';
            });
        }

        function sendBulkSMS() {
            const generatedSMS = document.getElementById('generated-sms').innerText;
            const selectedRecipients = Array.from(document.querySelectorAll('input[name="recipients[]"]:checked')).map(cb => cb.value);
            
            fetch('/send-bulk-sms', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ sms_content: generatedSMS, recipients: selectedRecipients })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            });
        }
    </script>
</body>
</html>
