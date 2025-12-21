<!DOCTYPE html>
<html>
<head>
    <title>{{ $details['title'] }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #ff4b5c; /* Red */
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .content {
            padding: 20px;
        }
        .content h2 {
            color: #ff4b5c;
            margin-bottom: 15px;
        }
        .content p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th {
            background-color: #ff4b5c; /* Red */
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .footer {
            background-color: #ff4b5c; /* Red */
            color: #ffffff;
            text-align: center;
            padding: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $details['title'] }}</h1>
        </div>
        <div class="content">
            <h2>{{ $details['body'] }}</h2>
            <p><strong>Tên Khách Hàng : </strong> {{ $details['customer']['name'] }}</p>
            <p><strong>Email : </strong> {{ $details['customer']['email'] }}</p>
            <p><strong>Phone : </strong> {{ $details['customer']['phone'] }}</p>
            <p><strong>Cửa hàng : </strong> {{ $details['showroom']['address'] }}</p>

            <table class="table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Ngày</th>
                        <th>Giờ</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $details['product']['name'] }}</td>
                        <td>{{ $details['booking']['date'] }}</td>
                        <td>{{ $details['booking']['time'] }}</td>
                        <td>{{ number_format($details['booking']['price'], 2) }} VND</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }}  All rights reserved.</p>
        </div>
    </div>
</body>
</html>


