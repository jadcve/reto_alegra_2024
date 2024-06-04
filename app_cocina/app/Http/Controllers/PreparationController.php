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

            // Crear la lista de ingredientes necesarios
            foreach ($menu->ingredients as $ingredient) {
                $ingredientsNeeded[$ingredient->ingredient_name] = $ingredient->quantity * $quantity;
            }

            // Crear cliente HTTP
            $client = new Client();

            // Solicitar ingredientes a la Bodega
            $response = $client->post('http://bodega-app:9000/api/request-ingredients', [
                'json' => [
                    'ingredients' => $ingredientsNeeded
                ]
            ]);

            if ($response->getStatusCode() != 200) {
                return response()->json(['message' => 'Failed to get ingredients from warehouse', 'error' => (string) $response->getBody()], 400);
            }

            return response()->json(['message' => 'Order processed successfully', 'order_id' => $orderId, 'menu' => $menu->name], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to process order', 'error' => $e->getMessage()], 500);
        }
    }
}
