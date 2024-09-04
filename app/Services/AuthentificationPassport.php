<?php


namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthentificationPassport implements AuthentificationServiceInterface
{
    public function authenticate(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            $user->token()->revoke();
        }
    }
}
