<?php

namespace App\Http\Middleware;

use Closure;

class ApiKeyValidate
{


    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @author Alain Diaz
     * @return mixed
     */

    /**
     * Este middleware se encarga de validar la API Key en las peticiones HTTP.
     * Si la API Key no está presente o no es válida, se devuelve un error 401.
     */

    public function handle($request, Closure $next)
    {

        if (!$request->headers->get('x-api-key')) {
            return response()->json([
                'status' => 401,
                'message' => 'Acceso no autorizado',
            ], 401);
        }

        if ($request->headers->get('x-api-key')) {
            $api_key = env('API_KEY');
            if ($request->headers->get('x-api-key') != $api_key) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Acceso no autorizado',
                ], 401);
            }
        }

        return $next($request);
    }
}
