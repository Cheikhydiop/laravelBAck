<?php

namespace App\Services;

use App\Models\Client;

interface ClientServiceInterface
{
    public function findClientById($id): ?Client;

    public function findClientByTelephone($telephone): ?Client;

    public function createClient(array $data): Client;

    public function updateClient($id, array $data): ?Client;

    public function deleteClient($id): bool;
}
