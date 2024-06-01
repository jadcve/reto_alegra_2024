<?php

namespace App\Traits;


/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ApiResponserTrait
{

    /**
     * Return a success JSON response.
     *
     * @param  array|string  $data
     * @param  string  $message
     * @param  int|null  $code
     * @author Alain DIaz
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(string $message = null, int $code, $data = null, $extra = null)
    {
        $jsonResponse = [
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
            'code' => $code
        ];

        if(is_array($extra)){
            $jsonResponse = array_merge($jsonResponse, $extra);
        }
        return response()->json($jsonResponse, $code);
    }



    /**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array|string|null  $data
     * @author Alain DIaz
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = null, int $code, $data = null)
    {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => $data,
            'code' => $code
        ], $code);
    }


}
