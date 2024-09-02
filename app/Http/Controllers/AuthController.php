<?php

namespace App\Http\Controllers;

use App\Enums\StatusResponseEnum;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Traits\RestResponseTrait;

class AuthController extends Controller
{
    use RestResponseTrait; 

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="prenom", type="string"),
     *             @OA\Property(property="photo", type="string"),
     *             @OA\Property(property="login", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="client.id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ClientResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $userRequest = $request->only('nom', 'prenom', 'photo', 'login', 'password', 'client.id');
            
            $clientId = $userRequest['client.id']; 
            if (!$clientId) {
                throw new Exception("L'ID client n'est pas fourni ou invalide.");
            }
            
            $userRequest['role_id'] = 2; // Assuming role_id 2 is for users
            $userRequest['password'] = Hash::make($userRequest['password']);
            
            $user = User::create($userRequest);
            
            $client = Client::find($clientId);
            if (!$client) {
                throw new Exception("Client non trouvé");
            }
            
            $client->user()->associate($user);
            $client->save();
            
            DB::commit();
            
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Compte créé avec succès', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }
    
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Login a user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="login", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid login or password",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login ou mot de passe incorrect")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('login', 'password');
        
        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;
            return response()->json(['token' => $token], 200);
        }
        
        return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Login ou mot de passe incorrect', 401);
    }
    
    /**
     * @OA\Get(
     *     path="/api/v1/profile",
     *     summary="Get user profile",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function profile(Request $request)
    {
        // Logic to retrieve user profile would go here
        // This typically involves getting the authenticated user and returning their details.
        $user = $request->user();
        return $this->sendResponse(new UserResource($user), StatusResponseEnum::SUCCESS);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logout a user",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You have been successfully logged out!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // Vérifiez si l'utilisateur est authentifié
        if (!$request->user()) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }
    
        // Révoquez le token de l'utilisateur
        $request->user()->token()->revoke();
    
        // Retournez une réponse de succès
        return response()->json([
            'message' => 'Vous avez été déconnecté avec succès !'
        ], 200);
    }
    

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="List all users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/UserResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $users = User::all();
        return $this->sendResponse(UserResource::collection($users), StatusResponseEnum::SUCCESS);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="prenom", type="string"),
     *             @OA\Property(property="photo", type="string"),
     *             @OA\Property(property="login", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="role_id", type="integer"),
     *             @OA\Property(property="active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            return $this->sendResponse(new UserResource($user), StatusResponseEnum::SUCCESS, 'Utilisateur créé avec succès', 201);
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }
}
