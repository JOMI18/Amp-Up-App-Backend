<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
        }
        .card-header {
            background-color:#F55139;
            color: #fff;
            border-radius: 10px 10px 0 0;
            padding: 15px;
            text-align: center;
        }
        .card-body {
            padding: 20px;
            background-color: #fff;
            border-radius: 0 0 10px 10px;
        }
        .email-content {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
        .button {
            display: inline-block;
            background-color: #F55139;
            color: #fff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #ed3519;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div  class="card-header">
                <h2> Email Notifications</h2>
            </div>
            <div class="card-body">
                <div class="email-content">
                    <p>Hello {{ $data['name']}},</p>
                    {{-- <p>This is a notification email from our website. We have some important information to share with you:</p> --}}
                    <p>{!! $data['message'] !!}</p>
                    <p>Thank you for using our service.</p>
                    <p>Best regards,<br>{{ config('app.name') }}</p>
                </div>
                <div class="text-center mt-4">
                    <a style="color: #fff" href="{{ url(config('app.url')) }}" class="button">Visit Our Website</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
