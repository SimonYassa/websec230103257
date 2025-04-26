<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; border: 1px solid #ddd;">
        <h2 style="color: #007bff;">Verify Your Email Address</h2>
        <p>Dear {{ $name }},</p>
        <p>Thank you for registering! Please click on the button below to verify your email address:</p>
        
        <p style="text-align: center;">
            <a href="{{ $link }}" style="display: inline-block; background-color: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;">Verify Email Address</a>
        </p>
        
        <p>If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
        <p style="word-break: break-all; background-color: #eee; padding: 10px; border-radius: 3px;">{{ $link }}</p>
        
        <p>If you did not create an account, no further action is required.</p>
        <p>Regards,<br>Your Application Team</p>
    </div>
</body>
</html>
