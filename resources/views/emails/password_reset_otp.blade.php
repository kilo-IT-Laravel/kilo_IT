<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        /* Basic reset */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #4CAF50; 
            padding: 20px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-content {
            padding: 30px 20px;
        }
        .otp-code {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50; 
            text-align: center;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            padding: 12px 25px;
            margin: 30px 0 10px;
            background-color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
            border-radius: 4px;
            text-align: center;
            transition: background-color 0.3s;
        }
        .cta-button:hover {
            background-color: #45a049; 
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin: 20px 0;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="email-header">
                <h1>Password Reset Request</h1>
            </div>
            <div class="email-content">
                <p>Dear User,</p>
                <p>We received a request to reset your password. Please use the OTP code below to complete the password reset process.</p>
                <div class="otp-code">{{ $otp }}</div>
                <p>This OTP is valid for <strong>5 minutes</strong>. If you did not request this reset, please ignore this email.</p>
            </div>
        </div>
        <div class="footer">
            <p>If you need help, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
