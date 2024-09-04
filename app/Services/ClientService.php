<?php

namespace App\Services;

use App\Repositories\ClientRepositoryInterface;
use App\Models\Client;

class ClientService implements ClientServiceInterface
{
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function findClientById($id): ?Client
    {
        dd($id);
        return $this->clientRepository->find($id);
    }

    public function findClientByTelephone($telephone): ?Client
    {
        return $this->clientRepository->findByTelephone($telephone);
    }

    public function createClient(array $data): Client
    {
        return $this->clientRepository->create($data);
    }

    public function updateClient($id, array $data): ?Client
    {
        return $this->clientRepository->update($id, $data);
    }

    public function deleteClient($id): bool
    {
        return $this->clientRepository->delete($id);
    }
}
