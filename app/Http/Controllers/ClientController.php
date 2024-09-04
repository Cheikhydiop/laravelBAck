<?php

namespace App\Http\Controllers;

use App\Services\ClientServiceInterface;
use App\Services\UploadService;
use App\Services\QrCodeService; // Ajoutez cette ligne
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Enums\StatusResponseEnum;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoyaltyCardMail;

class ClientController extends Controller
{
    protected $uploadService;
    protected $clientService;
    protected $qrCodeService; // Ajoutez cette ligne

    // Injectez les services dans le constructeur
    public function __construct(ClientServiceInterface $clientService, UploadService $uploadService, QrCodeService $qrCodeService)
    {
        $this->clientService = $clientService;
        $this->uploadService = $uploadService;
        $this->qrCodeService = $qrCodeService; // Ajoutez cette ligne
    }

    public function store(ClientRequest $request)
    {
        try {
            DB::beginTransaction();
            $clientRequest = $request->only('surname', 'adresse', 'telephone', 'email'); // Ajoutez 'email' ici
    
            if ($request->hasFile('photo')) {
                $clientRequest['photo'] = $this->uploadService->uploadImage($request->file('photo'));
            }
    
            $client = Client::create($clientRequest);
    
            if ($request->has('user')) {
                $roleId = $request->input('user.role.id');
                $role = Role::find($roleId);
                $user = User::create([
                    'nom' => $request->input('user.nom'),
                    'prenom' => $request->input('user.prenom'),
                    'login' => $request->input('user.login'),
                    'password' => Hash::make($request->input('user.password')),
                    'photo' => $request->input('user.photo'),
                    'role_id' => $role->id
                ]);
                $client->user()->associate($user);
                $client->save();
            }
    
            DB::commit();
    
            // Vérifiez que l'adresse e-mail est définie
            if (!$client->email) {
                return $this->sendResponse(['error' => 'L\'adresse e-mail du client est manquante.'], StatusResponseEnum::ECHEC, 'Erreur lors de l\'envoi de l\'e-mail', 400);
            }
    
            // Utilisez le service pour générer la carte de fidélité
            $this->qrCodeService->generateLoyaltyCard($client);
    
            // Envoyez l'e-mail avec la carte de fidélité en pièce jointe
            Mail::to($client->email)->send(new LoyaltyCardMail($client));
    
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Client créé avec succès', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 'Une erreur est survenue', 500);
        }
    }
    

    public function show($id)
    {
        $client = $this->clientService->findClientById($id);
        if ($client) {
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Client trouvé', 200);
        } else {
            return $this->sendResponse(['message' => 'Client non trouvé.'], StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }
    }

    public function getByTelephone(Request $request)
    {
        $request->validate([
            'telephone' => 'required|string|size:9',
        ]);

        $telephone = $request->input('telephone');
        $client = $this->clientService->findClientByTelephone($telephone);

        if ($client) {
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Client trouvé', 200);
        } else {
            return $this->sendResponse(['message' => 'Client non trouvé.'], StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }
    }

    protected function sendResponse($data, $status, $message, $httpCode)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $httpCode);
    }
}