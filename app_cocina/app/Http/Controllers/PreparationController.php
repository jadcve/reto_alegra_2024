<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PreparationController extends Controller
{
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
                    'order_id' => $orderId,
                    'ingredients' => $ingredientsNeeded
                ]
            ]);

            if ($response->getStatusCode() != 200) {
                return response()->json(['message' => 'Failed to request ingredients from warehouse', 'error' => (string) $response->getBody()], 400);
            }

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['not_available']) && !empty($responseBody['not_available'])) {
                Log::info("message: algunos ingredientes no están disponibles, orden en proceso");
                // Actualizar el estado de la orden a "En proceso"
                $client->post('http://gerente-web/api/orders/update-status', [
                    'json' => [
                        'order_id' => $orderId,
                        'status' => 'En proceso'
                    ]
                ]);

                return response()->json(['message' => 'Waiting for ingredients', 'not_available' => $responseBody['not_available']], 202);
            }

            // Actualizar el estado de la orden a "Despachada"
            Log::info("message: enviando a gerente que la orden está despachada");
            $client->post('http://gerente-web/api/orders/update-status', [
                'json' => [
                    'order_id' => $orderId,
                    'status' => 'Despachada'
                ]
            ]);

            return response()->json(['message' => 'Order processed successfully', 'order_id' => $orderId, 'menu' => $menu->name], 200);
        } catch (Exception $e) {
            Log::error('Failed to process order', ['error' => $e->getMessage(), 'order_id' => $orderId]);
            return response()->json(['message' => 'Failed to process order', 'error' => $e->getMessage()], 500);
        }
    }
}


