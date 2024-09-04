<?php

namespace App\Services;

use App\Models\Client;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateLoyaltyCard(Client $client)
    {
        // Préparer les informations à encoder dans le QR code
        $data = [
            'surname' => $client->surname,
            'telephone' => $client->telephone,
            'adresse' => $client->adresse,
        ];

        $jsonData = json_encode($data);

        $qrCode = QrCode::format('png')->size(200)->generate($jsonData);
        $base64QrCode = base64_encode($qrCode);

        $client->loyalty_card_qr_code = 'data:image/png;base64,' . $base64QrCode;
        $client->save();
    }
}
