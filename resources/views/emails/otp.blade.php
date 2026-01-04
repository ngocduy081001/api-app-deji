<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã xác thực OTP</title>
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
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .otp-code {
            background-color: #E70214;
            color: white;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            letter-spacing: 8px;
            margin: 30px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Mã xác thực OTP</h1>
        </div>

        <p>Xin chào,</p>

        <p>Bạn đã yêu cầu mã xác thực OTP để đăng ký tài khoản. Vui lòng sử dụng mã sau:</p>

        <div class="otp-code">
            {{ $otpCode }}
        </div>

        <p><strong>Lưu ý:</strong></p>
        <ul>
            <li>Mã OTP này có hiệu lực trong 10 phút</li>
            <li>Không chia sẻ mã này với bất kỳ ai</li>
            <li>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này</li>
        </ul>

        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
            <p>&copy; {{ date('Y') }} Deji Việt Nam. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>

</html>
