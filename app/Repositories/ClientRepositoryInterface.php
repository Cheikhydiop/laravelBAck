<?php

// app/Repositories/ClientRepositoryInterface.php
namespace App\Repositories;

use App\Models\Client;

interface ClientRepositoryInterface
{
    public function find($id): ?Client;
    public function findByTelephone($telephone): ?Client;
    public function create(array $data): Client;
    public function update($id, array $data): ?Client;
    public function delete($id): bool;
}
