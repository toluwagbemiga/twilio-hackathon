<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Environment Variables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Set Environment Variables</h1>
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form action="{{ route('environment.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="TWILIO_SID" class="form-label">Twilio SID</label>
            <input type="text" class="form-control" id="TWILIO_SID" name="TWILIO_SID" value="{{ env('TWILIO_SID') }}" required>
        </div>
        <div class="mb-3">
            <label for="TWILIO_AUTH_TOKEN" class="form-label">Twilio Auth Token</label>
            <input type="text" class="form-control" id="TWILIO_AUTH_TOKEN" name="TWILIO_AUTH_TOKEN" value="{{ env('TWILIO_AUTH_TOKEN') }}" required>
        </div>
        <div class="mb-3">
            <label for="TWILIO_PHONE_NUMBER" class="form-label">Twilio Phone Number</label>
            <input type="text" class="form-control" id="TWILIO_PHONE_NUMBER" name="TWILIO_PHONE_NUMBER" value="{{ env('TWILIO_PHONE_NUMBER') }}" required>
        </div>
        <div class="mb-3">
            <label for="GEMINI_API_KEY" class="form-label">Gemini API Key</label>
            <input type="text" class="form-control" id="GEMINI_API_KEY" name="GEMINI_API_KEY" value="{{ env('GEMINI_API_KEY') }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
</body>
</html>
