<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Auth\AuthModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Respuesta\JSONResponseController;

class AuthController extends JSONResponseController

{

    public function __construct()
    {
//        $this->middleware('auth:sanctum', ['only' => ['cerrarSesion']]);
    }

    public function register(Request $request): JsonResponse
    {
        // Validar la solicitud
         Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    

        // Crear el usuario
       User::create([
            'username' => Str::upper($request->username),
            'password' =>  bcrypt($request->password),
        ]);

       
        return $this->sendResponse(200, 1, 'Sesión creada.');
        
    }
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->only(['username', 'password']), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $tituloValidador = 'Error en la Validación';

        if ($validator->fails()) {
            return $this->sendResponse(200, 3, $tituloValidador, $validator->errors());
        }

        $username = Str::upper($request->username);
        $password = Str::upper($request->password);
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $autenticacionModel = new AuthModel();

        $resultado = $autenticacionModel->validarInicioSesion($username, $password, $passwordHash);

        if ($resultado->estado != 1) {
            return $this->sendResponse(200, $resultado->estado, $tituloValidador, $resultado->mensaje);
        }

        if (!Auth::attempt(['username' => $username ,'password' => $password])) {
            return $this->sendResponse(200, 0, 'Usuario o contraseña incorrectos.');
        }

        $fechaExpiracion = Carbon::now()->addHours(8);
        $usuario = Auth::user();
        $token = $usuario->createToken('personal', ["*"], $fechaExpiracion)->plainTextToken;

       
        $datosUsuario = [
            'usuario' => $usuario->username,
            'token' => $token,
            'fecha_expiracion' => $fechaExpiracion
        ];

        return $this->sendResponse(200, 1, 'Sesión creada.', $datosUsuario);
    }

}
