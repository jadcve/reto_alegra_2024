<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\PendingOrder;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{

    public function requestIngredients(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $ingredientsNeeded = $request->input('ingredients');
            $notAvailable = [];
            $apiToken = 'api-token';  // token de la API de Alegra

            foreach ($ingredientsNeeded as $ingredientName => $quantity) {
                $ingredient = Ingredient::where('name', $ingredientName)->first();

                if ($ingredient->quantity < $quantity) {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiToken,
                        'Content-Type' => 'application/json'
                    ])->get('https://recruitment.alegra.com/api/farmers-market/buy', [
                        'ingredient' => $ingredientName
                    ]);
                    Log::info("message: response from Alegra API: " . $response->body());

                    if ($response->successful() && $response->json('quantitySold') > 0) {
                        $ingredient->quantity += $response->json('quantitySold');
                        $ingredient->save();
                    } else {
                        $notAvailable[$ingredientName] = $quantity - $ingredient->quantity;
                    }
                }

                if ($ingredient->quantity < $quantity) {
                    $notAvailable[$ingredientName] = $quantity - $ingredient->quantity;
                } else {
                    $ingredient->quantity -= $quantity;
                    $ingredient->save();
                }
            }

            if (!empty($notAvailable)) {
                // Almacenar la orden pendiente en una tabla de órdenes pendientes
                PendingOrder::updateOrCreate(
                    ['order_id' => $orderId],
                    ['ingredients_needed' => json_encode($notAvailable)]
                );

                return response()->json(['message' => 'Some ingredients are not available', 'not_available' => $notAvailable], 200);
            }

            // Actualizar el estado de la orden a "Despachada"
            Log::info("message: enviando a gerente que la orden está despachada");
            $client = new Client();
            $client->post('http://gerente-web/api/orders/update-status', [
                'json' => [
                    'order_id' => $orderId,
                    'status' => 'Despachada'
                ]
            ]);

            return response()->json(['message' => 'Ingredients provided successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to process ingredient request', 'error' => $e->getMessage()], 500);
        }
    }

    public function checkPendingOrders()
    {
        $pendingOrders = PendingOrder::all();
        $client = new Client();
        $apiToken = 'api-token';  // token de la API de Alegra

        foreach ($pendingOrders as $pendingOrder) {
            $ingredientsNeeded = json_decode($pendingOrder->ingredients_needed, true);
            $allIngredientsAvailable = true;

            foreach ($ingredientsNeeded as $ingredientName => $quantity) {
                $ingredient = Ingredient::where('name', $ingredientName)->first();

                if ($ingredient->quantity < $quantity) {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiToken,
                        'Content-Type' => 'application/json'
                    ])->get('https://recruitment.alegra.com/api/farmers-market/buy', [
                        'ingredient' => $ingredientName
                    ]);
                    Log::info("message: response from Alegra API: " . $response->body());

                    if ($response->successful() && $response->json('quantitySold') > 0) {
                        $ingredient->quantity += $response->json('quantitySold');
                        $ingredient->save();
                    } else {
                        $allIngredientsAvailable = false;
                    }
                }

                if ($ingredient->quantity < $quantity) {
                    $allIngredientsAvailable = false;
                    $pendingOrder->ingredients_needed = json_encode($ingredientsNeeded);
                    $pendingOrder->save();
                } else {
                    $ingredient->quantity -= $quantity;
                    $ingredient->save();
                }
            }

            if ($allIngredientsAvailable) {
                // Actualizar el estado de la orden a "Despachada"
                $client->post('http://gerente-web/api/orders/update-status', [
                    'json' => [
                        'order_id' => $pendingOrder->order_id,
                        'status' => 'Despachada'
                    ]
                ]);

                $pendingOrder->delete();
            }
        }
    }
}
