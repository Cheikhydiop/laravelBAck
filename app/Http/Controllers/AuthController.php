<?php

namespace App\Http\Controllers;

use App\Enums\StatusResponseEnum;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RegistreRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\User;
use App\Services\CustomTokenGenerator;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    use RestResponseTrait; 

    //Cet end point va permettre de créer un compte utilisateur pour un client
    //le client doit exister et les informations de l'utilisateur doivent etre valides
    public function register(RegisterRequest $request){
        try {
            DB::beginTransaction();
            $userRequest = $request->only('nom', 'prenom', 'photo', 'login', 'password', 'client');

             // Vérification de l'ID du client
            $clientId = $request->input('client.id'); 
            if (!$clientId) {
                throw new Exception("L'ID client n'est pas fourni ou invalide.");
            }

            //ajouter le role boutiquier
            $userRequest['role_id'] = 2;

             // Hachage du mot de passe
            $userRequest['password'] = Hash::make($userRequest['password']);

             // Création de l'utilisateur
            $user = User::create($userRequest);

            $client = Client::find($clientId);

            if (!$client) {
                throw new Exception("Client non trouvé");
            }
          
            $client->user()->associate($user);
            $client->save();
            
           
            DB::commit();
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Compte créer avec succès', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }
    public function login(LoginRequest $request)
    {
        // Récupérer les informations de connexion
        $credentials = $request->only('login', 'password');
    
        // Tenter d'authentifier l'utilisateur avec les informations fournies
        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            // Si les informations de connexion sont incorrectes
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Login ou mot de passe incorrect', 401);
        }
    }
    

    public function profile(Request $request){
        
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();
        return $this->sendResponse(null, StatusResponseEnum::SUCCESS, 'You have been successfully logged out!');
    }


    public function index()
    {
        $users = User::all();
        return $this->sendResponse(UserResource::collection($users), StatusResponseEnum::SUCCESS);
    }

    public function store(Request $request)
    {
        try {
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'photo' => $request->photo,
                'login' => $request->login,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'active' => $request->active,
            ]);

            return $this->sendResponse(new UserResource($user), StatusResponseEnum::SUCCESS);
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->sendResponse(['error' => 'User not found'], StatusResponseEnum::ECHEC, 404);
        }

        return $this->sendResponse(new UserResource($user), StatusResponseEnum::SUCCESS);
    }

    /*public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->all());

            return $this->sendResponse(new UserResource($user), \App\Enums\StateEnum::SUCCESS);
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], \App\Enums\StateEnum::ECHEC, 500);
        }
    }*/

    /*public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->sendResponse(['error' => 'User not found'], \App\Enums\StateEnum::ECHEC, 404);
        }

        $user->delete();
        return $this->sendResponse(null, \App\Enums\StateEnum::SUCCESS);
    }*/
}
