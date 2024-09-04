<?php

namespace App\Services;

interface AuthentificationServiceInterface
{
    public function authenticate(array $credentials): bool;
    public function logout();
}
