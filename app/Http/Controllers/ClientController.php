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

/**
 * @OA\Tag(
 *     name="Clients",
 *     description="Client related operations"
 * )
 */
class ClientController extends Controller
{
    use RestResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/v1/clients",
     *     summary="Create a new client",
     *     tags={"Clients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="surname", type="string", example="Doe"),
     *             @OA\Property(property="adresse", type="string", example="123 Main St"),
     *             @OA\Property(property="telephone", type="string", example="1234567890"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="nom", type="string", example="John"),
     *                 @OA\Property(property="prenom", type="string", example="Doe"),
     *                 @OA\Property(property="login", type="string", example="johndoe"),
     *                 @OA\Property(property="password", type="string", example="password123"),
     *                 @OA\Property(property="photo", type="string", example="photo.jpg"),
     *                 @OA\Property(property="role.id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ClientResource"),
     *             @OA\Property(property="message", type="string", example="Client créé avec succès")
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
    public function store(ClientRequest $request)
    {
        // Method implementation
    }

    /**
     * @OA\Get(
     *     path="/api/v1/clients",
     *     summary="Get a list of clients with optional filters",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="comptes",
     *         in="query",
     *         description="Filter by account existence",
     *         required=false,
     *         @OA\Schema(type="string", enum={"oui", "non"})
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filter by account activity",
     *         required=false,
     *         @OA\Schema(type="string", enum={"oui", "non"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of clients",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ClientResource")),
     *             @OA\Property(property="message", type="string", example="Liste des clients.")
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
    public function index(Request $request)
    {
        // Method implementation
    }

    /**
     * @OA\Get(
     *     path="/api/v1/clients/telephone",
     *     summary="Get a client by telephone number",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="telephone",
     *         in="query",
     *         description="Client telephone number",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client found",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ClientResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Aucun client trouvé avec ce numéro de téléphone.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Veuillez renseigner un numéro de téléphone.")
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
    public function getByTelephone(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'telephone' => 'required|string|size:9',
        ]);

        // Retrieve the telephone number from the request
        $telephone = $request->input('telephone');

        // Find the client by telephone number
        $client = Client::where('telephone', $telephone)->first();

        if ($client) {
            return response()->json($client, 200);
        } else {
            return response()->json(['message' => 'Client not found.'], 404);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/v1/clients/{id}/users",
     *     summary="Get a client by ID",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Client ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client found",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ClientResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Client non trouvé.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="L'identifiant doit être un nombre valide.")
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
    public function getById($id)
    {
        // Method implementation
    }

    /**
 * @OA\Get(
 *     path="/api/v1/clients/{id}/user",
 *     summary="Get a client with user details by ID",
 *     tags={"Clients"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Client ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client with user found",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", ref="#/components/schemas/ClientResource")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Client not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Client non trouvé.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="L'identifiant doit être un nombre valide.")
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
     
    public function clientWithUser($id)
    {
        // Trouver l'utilisateur par ID, lancer une exception 404 si non trouvé
        $user = User::findOrFail($id);
    
        // Récupérer le client associé
        $client = $user->client; // Supposons une relation un-à-un
    
        if (!$client) {
            return response()->json(['message' => 'Client non trouvé.'], 404);
        }
    
        // Retourner les détails du client
        return response()->json(new ClientResource($client), 200);
    }
    
}
