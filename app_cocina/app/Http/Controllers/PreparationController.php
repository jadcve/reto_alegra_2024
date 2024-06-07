<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PreparationController extends Controller
{
    // public function prepare(Request $request)
    // {
    //     try {
    //         Log::info("message: llegue a cocina");
    //         $orderId = $request->input('order_id');
    //         $quantity = $request->input('quantity');

    //         $menu = Menu::inRandomOrder()->first();
    //         $ingredientsNeeded = [];

    //         foreach ($menu->ingredients as $ingredient) {
    //             $ingredientsNeeded[$ingredient->ingredient_name] = $ingredient->quantity * $quantity;
    //         }

    //         Log::info("message: ingredientes necesarios " . json_encode($ingredientsNeeded));

    //         $client = new Client();
    //         $response = $client->post('http://bodega-web/api/request-ingredients', [
    //             'json' => [
    //                 'ingredients' => $ingredientsNeeded
    //             ]
    //         ]);

    //         if ($response->getStatusCode() != 200) {
    //             return response()->json(['message' => 'Failed to get ingredients from warehouse', 'error' => (string) $response->getBody()], 400);
    //         }

    //         return response()->json(['message' => 'Order processed successfully', 'order_id' => $orderId, 'menu' => $menu->name], 200);
    //     } catch (Exception $e) {
    //         return response()->json(['message' => 'Failed to process order', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function prepare(Request $request)
    {
        try {
            Log::info("message: llegue a cocina");
            $orderId = $request->input('order_id');
            $quantity = $request->input('quantity');

            // Seleccionar un menú aleatoriamente
            $menu = Menu::inRandomOrder()->first();
            $ingredientsNeeded = [];

            foreach ($menu->ingredients as $ingredient) {
                $ingredientsNeeded[$ingredient->ingredient_name] = $ingredient->quantity * $quantity;
            }

            Log::info("message: ingredientes necesarios " . json_encode($ingredientsNeeded));

            $client = new Client();
            $response = $client->post('http://bodega-web/api/request-ingredients', [
                'json' => [
                    'ingredients' => $ingredientsNeeded
                ]
            ]);

            if ($response->getStatusCode() != 200) {
                return response()->json(['message' => 'Failed to get ingredients from warehouse', 'error' => (string) $response->getBody()], 400);
            }

            $responseBody = json_decode($response->getBody(), true);

            // Verificar si hay ingredientes no disponibles
            if (isset($responseBody['not_available']) && !empty($responseBody['not_available'])) {
                Log::info("message: algunos ingredientes no están disponibles");
                return response()->json(['message' => 'Waiting for ingredients', 'not_available' => $responseBody['not_available']], 202);
            }

            // Actualizar el estado a "Despachada"
            Log::info("message: enviando a gerente que la orden está despachada");
            $client->post('http://gerente-web/api/orders/update-status', [
                'json' => [
                    'order_id' => $orderId,
                    'status' => 'Despachada'
                ]
            ]);

            return response()->json(['message' => 'Order processed successfully', 'order_id' => $orderId, 'menu' => $menu->name], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to process order', 'error' => $e->getMessage()], 500);
        }
    }





}


