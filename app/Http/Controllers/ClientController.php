<?php

namespace App\Http\Controllers;

use App\Enums\StatusResponseEnum;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    use RestResponseTrait;
    public function store(ClientRequest $request)
    {
        try {
            DB::beginTransaction();
            $clientRequest = $request->only('surname','adresse','telephone');
            $client= Client::create($clientRequest);
            if ($request->has('user')){
                $roleId = $request->input('user.role.id');
                $role = Role::find($roleId);
                $user = User::create([
                    'nom' => $request->input('user.nom'),
                    'prenom' => $request->input('user.prenom'),
                    'login' => $request->input('user.login'),
                    'password' => $request->input('user.password'),
                    'photo' => $request->input('user.photo'),
                    'role_id' => $role->id
                ]);
                $client->user()->associate($user);
                $client->save();
                //$user->client()->save($client);
            }
            DB::commit();
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Client créé avec succès', 201);
        }catch (Exception $e){
            DB::rollBack();
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }


    public function index(Request $request) {
        try{
            $clients = Client::query();
             // Filtrer par comptes (avec ou sans utilisateur)
            if ($request->has('comptes')) {
                $value = strtolower($request->get('comptes'));
                
                if ($value === 'oui') {
                    // Clients avec un compte utilisateur (user_id non null)
                    $clients->whereHas('user');
                } elseif ($value === 'non') {
                    // Clients sans compte utilisateur (user_id null)
                    $clients->doesntHave('user');
                }
            }

             // Filtrer par activité du compte utilisateur
            if ($request->has('active')) {
                $value = strtolower($request->get('active'));
                
                if ($value === 'oui') {
                    // Clients avec un compte utilisateur actif (active = 'OUI')
                    $clients->whereHas('user', function($query) {
                        $query->where('active', 'OUI');
                    });
                } elseif ($value === 'non') {
                    // Clients avec un compte utilisateur inactif (active != 'OUI')
                    $clients->whereHas('user', function($query) {
                        $query->where('active', 'NON');
                    });
                }
            }

            $clients = $clients->get();

            // Retourner la réponse
            if ($clients->isNotEmpty()) {
                return $this->sendResponse($clients, StatusResponseEnum::SUCCESS, 'Liste des clients.');
            } else {
                return $this->sendResponse([], StatusResponseEnum::SUCCESS, 'Pas de clients.');
            }
        }catch(Exception $e){
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, $e->getMessage(), 500);
        }
        
        //return $this->sendResponse(UserResource::collection($users), StatusResponseEnum::SUCCESS);
    }

    public function getByTelephone(Request $request) {
        $telephone = $request->input('telephone');
        if (!$telephone) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Veuillez renseigner un numéro de téléphone.', 400);
        }
        $client = Client::where('telephone', $telephone)->first();
        if (!$client) {
            return $this->sendResponse(null, StatusResponseEnum::SUCCESS, 'Aucun client trouvé avec ce numéro de téléphone.', 404);
        }
        return $this->sendResponse($client, StatusResponseEnum::SUCCESS, 'Client trouvé.', 200);
    }

    public function getById($id){
        // Vérifier si l'ID est valide
        if (!is_numeric($id)) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'L\'identifiant doit être un nombre valide.', 400);
        }

        // Récupérer le client par ID
        $client = Client::find($id);
        
        // Vérifier si le client existe
        if (!$client) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé.', 404);
        }

        // Retourner le client trouvé
        return $this->sendResponse($client, StatusResponseEnum::SUCCESS, 'Client trouvé avec succès.', 200);
    }

    public function clientWithUser($id){
        // Vérifier si l'ID est valide
        if (!is_numeric($id)) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'L\'identifiant doit être un nombre valide.', 400);
        }

        // Récupérer le client par ID avec la relation 'user'
        $client = Client::with('user')->find($id);
        
        // Vérifier si le client existe
        if (!$client) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé.', 404);
        }

        // Retourner le client trouvé
        return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Client trouvé avec succès.', 200);
    }

}
