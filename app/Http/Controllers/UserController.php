<?php
namespace App\Http\Controllers;

use App\Traits\RestResponseTrait;
use App\Enums\StatusResponseEnum;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use RestResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function getUsers(Request $request)
    {
        // Récupère les paramètres de filtrage depuis la requête
        $filters = $request->query();
        //Log::info('Request filters:', ['filters' => $filters]);

        // Filtrage basé sur 'active' et 'role_id'
        $query = User::query();

        // Appliquer le filtre sur 'active'
        if ($request->has('active')) {
            $activeFilter = strtoupper($request->query('active'));
            $query->where('active', $activeFilter);
        }

        
        if ($request->has('role_id')) {
            $roleFilter = $request->query('role_id');
            $query->where('role_id', $roleFilter);
        }

        // Exécute la requête
        $users = $query->get();

        //Log::info('Filtered users:', ['users' => $users->toArray()]);

        return $this->sendResponse($users, StatusResponseEnum::SUCCESS, 'Liste des utilisateurs');
    }
}
