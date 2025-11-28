<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .field {
            margin-bottom: 15px;
        }

        .field-label {
            font-weight: bold;
            color: #495057;
        }

        .field-value {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            white-space: pre-wrap;
        }

        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Contact Form Submission</h1>
            <p>You have received a new message from the ECL Contact Form.</p>
        </div>

        <div class="content">
            <div class="field">
                <div class="field-label">Name:</div>
                <div class="field-value">{{ $name }}</div>
            </div>

            <div class="field">
                <div class="field-label">Email:</div>
                <div class="field-value">{{ $email }}</div>
            </div>

            <div class="field">
                <div class="field-label">Subject:</div>
                <div class="field-value">{{ ucfirst($subject) }}</div>
            </div>

            <div class="field">
                <div class="field-label">Message:</div>
                <div class="field-value">{{ $messageContent }}</div>
            </div>
        </div>

        <div class="footer">
            <p>This email was sent from the ECL Contact Form on {{ config('app.name') }}.</p>
            <p>Sent at: {{ now()->format('Y-m-d H:i:s T') }}</p>
        </div>
    </div>
</body>

</html>
