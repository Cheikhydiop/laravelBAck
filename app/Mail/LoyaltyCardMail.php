<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LoyaltyCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function build()
    {
        // Chemin du répertoire où les fichiers PDF seront enregistrés
        $directoryPath = storage_path('app/public/loyalty_cards');

        // Vérifier si le répertoire existe, sinon le créer
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        // Générer le PDF à partir de la vue 'emails.loyalty_card'
        $pdf = Pdf::loadView('emails.loyalty_card', ['client' => $this->client]);
        $pdfPath = $directoryPath . '/client_' . $this->client->id . '.pdf';
        $pdf->save($pdfPath);

        return $this->view('emails.loyalty_card')
                    ->with(['client' => $this->client])
                    ->attach($pdfPath, [
                        'as' => 'loyalty_card.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
