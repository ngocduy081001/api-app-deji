<!DOCTYPE html>
<html>
<head>
    <title>Email Example</title>
    <style>
        /* Inline styles for email compatibility */
        .table {
            width: 100% !important;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .table th {
            background-color: #f4f4f4;
        }
        .img-fluid {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8f9fa;">
    <table style="width: 100%; border-collapse: collapse; background-color: #ffffff; margin: 20px auto; max-width: 600px;">
        <tr>
            <td style="padding: 20px;">
                <h1 style="font-size: 24px; color: #333333;">{{ $details['title'] }}</h1>

                <img src="https://i.pinimg.com/564x/9a/03/ad/9a03ad8167a4dd5891dab86f8576867c.jpg" alt="Example Image" class="img-fluid" style="margin-bottom: 20px;">

                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ddd;">Header 1</th>
                            <th style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ddd;">Header 2</th>
                            <th style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ddd;">Header 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">Data 1</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">Data 2</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">Data 3</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">Data 4</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">Data 5</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">Data 6</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
