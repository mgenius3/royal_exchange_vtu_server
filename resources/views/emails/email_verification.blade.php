<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #00CE7C, #1ABD1A);
            padding: 32px 24px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
        }
        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 32px 24px;
        }
        .verification-code {
            background: #f8f9fa;
            border: 2px dashed #00CE7C;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin: 24px 0;
        }
        .code {
            font-size: 36px;
            font-weight: 800;
            color: #00CE7C;
            letter-spacing: 4px;
            margin: 0;
            font-family: 'Courier New', monospace;
        }
        .instructions {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 16px;
            border-radius: 8px;
            margin: 24px 0;
        }
        .warning {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 16px;
            border-radius: 8px;
            margin: 24px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 24px;
            text-align: center;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #00CE7C, #1ABD1A);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            margin: 16px 0;
        }
        .highlight {
            color: #00CE7C;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Verification</h1>
            <p>Verify your email address to complete your registration</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $user->name }}!</h2>
            
            <p>Thank you for signing up! To complete your account setup, please verify your email address using the verification code below:</p>
            
            <div class="verification-code">
                <p style="margin: 0 0 8px 0; font-weight: 600; color: #666;">Your Verification Code</p>
                <h2 class="code">{{ $code }}</h2>
            </div>
            
            <div class="instructions">
                <h3 style="margin-top: 0; color: #1976d2;">How to verify:</h3>
                <ol>
                    <li>Open your mobile app</li>
                    <li>Navigate to the email verification screen</li>
                    <li>Enter the <span class="highlight">6-digit code</span> shown above</li>
                    <li>Tap "Verify Email" to complete the process</li>
                </ol>
            </div>
            
            <div class="warning">
                <h3 style="margin-top: 0; color: #f57c00;">Important:</h3>
                <ul style="margin-bottom: 0;">
                    <li>This code will expire in <strong>{{ $expires_in }} minutes</strong></li>
                    <li>Don't share this code with anyone</li>
                    <li>If you didn't request this, please ignore this email</li>
                </ul>
            </div>
            
            <p>If you have any issues or didn't request this verification, please contact our support team.</p>
        </div>
        
        <div class="footer">
            <p>This email was sent to {{ $user->email }}</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>