<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ClientResource",
 *     type="object",
 *     @OA\Property(property="surname", type="string"),
 *     @OA\Property(property="telephone", type="string"),
 *     @OA\Property(property="adresse", type="string"),
 *     @OA\Property(property="user", ref="#/components/schemas/UserResource")
 * )
 */
class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'surname' => $this->surname,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            // 'user' => new UserResource($this->whenLoaded('user')),
            'user' => new UserResource($this->user) 
        ];
    }
}
