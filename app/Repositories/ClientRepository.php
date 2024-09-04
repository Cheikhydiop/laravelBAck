<?php

// app/Repositories/ClientRepository.php
namespace App\Repositories;

use App\Models\Client;

class ClientRepository implements ClientRepositoryInterface
{
    public function find($id): ?Client
    {
        return Client::find($id);
    }

    public function findByTelephone($telephone): ?Client
    {
        // return Client::where('telephone', $telephone)->first();
        return Client::withTelephone($telephone)->first();

    }

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function update($id, array $data): ?Client
    {
        $client = $this->find($id);
        if ($client) {
            $client->update($data);
        }
        return $client;
    }

    public function delete($id): bool
    {
        $client = $this->find($id);
        if ($client) {
            return $client->delete();
        }
        return false;
    }
}
