<?php

namespace App\Traits;
use App\Enums\StatusResponseEnum;

trait RestResponseTrait
{
    public function sendResponse(mixed $data = null, StatusResponseEnum $status = StatusResponseEnum::SUCCESS, $message = 'Ressource non trouvée', $codeStatut = 200)
    {
        return response()->json([
            'data' =>$data,
            'status' =>  $status->value,
            'message' => $message,
        ],$codeStatut);
    }


    public function sendError($message, $errors = [], $code = 404)
    {
        return response()->json([
            'status' => $code,
            'data' => $errors,
            'message' => $message,
        ], $code);
    }
}
