<?php
namespace App\Http\Controllers;

use App\Http\Middleware\ApiResponseMiddleware;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        // Appliquer le middleware ApiResponseMiddleware
        $this->middleware(ApiResponseMiddleware::class);
    }

    /**
     * Affiche la liste des ressources.
     */
    public function getUsers(Request $request)
    {
        // Vérifie l'autorisation pour voir tous les utilisateurs
        $this->authorize('viewAny', User::class);

        // Récupère les paramètres de filtrage depuis la requête
        $filters = $request->query();

        // Filtrage basé sur 'active' et 'role_id'
        $query = User::query();

        // Appliquer le filtre sur 'active'
        if ($request->has('active')) {
            $activeFilter = strtoupper($request->query('active'));
            $query->where('active', $activeFilter);
        }

        // Appliquer le filtre sur 'role_id'
        if ($request->has('role_id')) {
            $roleFilter = $request->query('role_id');
            $query->where('role_id', $roleFilter);
        }

        // Exécute la requête
        $users = $query->get();

        // Retourner les données brutes
        return $users;
    }
}
