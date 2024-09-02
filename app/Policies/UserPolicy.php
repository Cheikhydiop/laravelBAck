<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
   
    public function viewAny(User $user): Response
    {
        return $user->role_id === 1
            ? Response::allow()
            : Response::deny('You do not have permission to view users.');
    }

  
    public function create(User $user): Response
    {
        return $user->role_id === 1
            ? Response::allow()
            : Response::deny('You do not have permission to create users.');
    }

   
    public function view(User $user, User $model): Response
    {
        return $user->role_id === 1
            ? Response::allow()
            : Response::deny('You do not have permission to view this user.');
    }

}
