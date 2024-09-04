uy8
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de Fidélité</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .card {
            width: 300px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: #fff;
            padding: 20px;
            text-align: center;
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            margin: 20px 0;
        }

        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }

        .reward {
            margin-top: 20px;
        }

        .card-footer {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border-radius: 0 0 10px 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Carte de Fidélité</h1>
            </div>
            <div class="card-body">
                <img src="{{ asset('path/to/logo.png') }}" alt="Logo">
                <img src="{{ dd($client->loyalty_card_qr_code )}}" alt="QR Code">
            </div>
            <div class="card-footer">
                Merci pour votre fidélité !
            </div>
        </div>
    </div>
</body>
</html>