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
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'x-api-key' => env('API_KEY'),
            ]
        ]);
    }

    public function requestIngredients(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $ingredientsNeeded = $request->input('ingredients');
            $notAvailable = $this->processIngredients($ingredientsNeeded);

            if (!empty($notAvailable)) {
                PendingOrder::updateOrCreate(
                    ['order_id' => $orderId],
                    ['ingredients_needed' => json_encode($notAvailable)]
                );

                return response()->json(['message' => 'Some ingredients are not available', 'not_available' => $notAvailable], 200);
            }

            $this->updateOrderStatus($orderId, 'Despachada');

            return response()->json(['message' => 'Ingredients provided successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to process ingredient request', 'error' => $e->getMessage()], 500);
        }
    }

    public function checkPendingOrders()
    {
        $pendingOrders = PendingOrder::all();

        foreach ($pendingOrders as $pendingOrder) {
            $ingredientsNeeded = json_decode($pendingOrder->ingredients_needed, true);
            $notAvailable = $this->processIngredients($ingredientsNeeded);

            if (empty($notAvailable)) {
                $this->updateOrderStatus($pendingOrder->order_id, 'Despachada');
                $pendingOrder->delete();
            } else {
                $pendingOrder->ingredients_needed = json_encode($notAvailable);
                $pendingOrder->save();
            }
        }
    }

    private function processIngredients($ingredientsNeeded)
    {
        $notAvailable = [];
        foreach ($ingredientsNeeded as $ingredientName => $quantity) {
            $ingredient = Ingredient::where('name', $ingredientName)->first();

            if ($ingredient->quantity < $quantity) {
                $this->buyFromAlegra($ingredientName);
            }

            if ($ingredient->quantity < $quantity) {
                $notAvailable[$ingredientName] = $quantity - $ingredient->quantity;
            } else {
                $ingredient->quantity -= $quantity;
                $ingredient->save();
            }
        }
        return $notAvailable;
    }

    private function buyFromAlegra($ingredientName)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->get('https://recruitment.alegra.com/api/farmers-market/buy', [
            'ingredient' => $ingredientName
        ]);

        Log::info("message: response from Alegra API: " . $response->body());

        if ($response->successful() && $response->json('quantitySold') > 0) {
            $ingredient = Ingredient::where('name', $ingredientName)->first();
            $ingredient->quantity += $response->json('quantitySold');
            $ingredient->save();
        }
    }

    private function updateOrderStatus($orderId, $status)
    {
        $this->client->post('http://gerente-web/api/orders/update-status', [
            'json' => [
                'order_id' => $orderId,
                'status' => $status
            ]
        ]);
    }

    public function getIngredients()
    {
        try {
            $ingredients = Ingredient::select('name', 'quantity')->get();
            return response()->json($ingredients, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve ingredients', 'error' => $e->getMessage()], 500);
        }
    }

}
