<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Vérifiez si la réponse est une instance de JsonResponse
        if ($response instanceof JsonResponse) {
            $originalData = $response->getData(true);

            // Vérifiez si la réponse contient des données de pagination
            if (isset($originalData['data'])) {
                $originalData['donner'] = $originalData['data'];
                unset($originalData['data']);

                // Ajoutez les clés 'status' et 'message'
                $originalData['status'] = 'success';
                $originalData['message'] = 'Données récupérées avec succès';

                return response()->json($originalData, $response->status());
            }

            // Ajoutez des clés 'status' et 'message' si la réponse ne contient pas de données de pagination
            $response = $response->setData([
                'donner' => $originalData,
                'status' => 'success',
                'message' => 'Données récupérées avec succès'
            ]);

            return $response;
        }

        // Si ce n'est pas une JsonResponse, retournez une réponse JSON par défaut
        return $this->sendResponse($response);
    }

    private function sendResponse($data = null, $status = 'success', $message = 'Ressource non trouvée', $code = 200)
    {
        return response()->json([
            'donner' => $data,
            'status' => $status,
            'message' => $message,
        ], $code);
    }

    private function sendError($message, $errors = [], $code = 404): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $errors,
        ], $code);
    }
}
