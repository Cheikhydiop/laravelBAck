<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ArticleResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="libelle", type="string", example="Article name"),
 *     @OA\Property(property="prix", type="number", format="float", example=19.99),
 *     @OA\Property(property="quantite", type="integer", example=100)
 * )
 */
class ArticleRessource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'libelle' => $this->libelle,
            'prix' => $this->prix,
            'quantite' => $this->quantite,
        ];
    }
}
