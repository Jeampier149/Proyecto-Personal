<?php

namespace App\Http\Controllers\Respuesta;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class JSONResponseController extends Controller
{
    protected array $response = [

    ];

    public function sendResponse(int $code, int|bool $success, string $message, $result = null): JsonResponse
    {
        $this->response = [
            'estado' => $success,
            'mensaje' => $message,
        ];
        if (!is_null($result)) {
            $this->response['datos'] = $result;
        }
        return response()->json($this->response, $code);
    }
}
