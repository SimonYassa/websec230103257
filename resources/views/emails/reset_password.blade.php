<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h2 {
            color: #007bff;
            margin-top: 0;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button-container {
            text-align: center;
        }
        .link {
            word-break: break-all;
            background-color: #eee;
            padding: 10px;
            border-radius: 3px;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- <img src="your-logo-url" alt="Company Logo" class="logo"> -->
            <h2>Reset Your Password</h2>
        </div>
        
        <p>Dear {{ $name }},</p>
        
        <p>You are receiving this email because we received a password reset request for your account.</p>
        
        <div class="button-container">
            <a href="{{ $resetLink }}" class="button">Reset Password</a>
        </div>
        
        <p>If you did not request a password reset, no further action is required.</p>
        
        <p>If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
        <p class="link">{{ $resetLink }}</p>
        
        <p>This password reset link will expire in 60 minutes.</p>
        
        <p>Regards,<br>Your Application Team</p>
        
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
