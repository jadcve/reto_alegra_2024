<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PreparationController extends Controller
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

    public function prepare(Request $request)
    {
        try {
            Log::info("message: llegue a cocina");

            $orderId = $request->input('order_id');
            $quantity = $request->input('quantity');
            $menuName = $request->input('menu_name');
            $menu = Menu::where('name', $menuName)->first();

            $ingredientsNeeded = $this->calculateIngredientsNeeded($menu->ingredients, $quantity);

            Log::info("message: ingredientes necesarios " . json_encode($ingredientsNeeded));

            $response = $this->sendRequestToWarehouse($orderId, $ingredientsNeeded);

            if ($response->getStatusCode() != 200) {
                return response()->json(['message' => 'Failed to request ingredients from warehouse', 'error' => (string) $response->getBody()], 400);
            }

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['not_available']) && !empty($responseBody['not_available'])) {
                Log::info("message: algunos ingredientes no están disponibles, orden en proceso");
                $this->updateOrderStatus($orderId, 'En proceso');
                return response()->json(['message' => 'Waiting for ingredients', 'not_available' => $responseBody['not_available']], 202);
            }

            $this->updateOrderStatus($orderId, 'Despachada');




            return response()->json(['message' => 'Order processed successfully', 'order_id' => $orderId, 'menu' => $menu->name], 200);
        } catch (Exception $e) {
            Log::error('Failed to process order', ['error' => $e->getMessage(), 'order_id' => $orderId]);
            return response()->json(['message' => 'Failed to process order', 'error' => $e->getMessage()], 500);
        }
    }

    private function calculateIngredientsNeeded($ingredients, $quantity)
    {
        $ingredientsNeeded = [];
        foreach ($ingredients as $ingredient) {
            $ingredientsNeeded[$ingredient->ingredient_name] = $ingredient->quantity * $quantity;
        }
        return $ingredientsNeeded;
    }

    private function sendRequestToWarehouse($orderId, $ingredientsNeeded)
    {
        return $this->client->post('http://bodega-web/api/request-ingredients', [
            'json' => [
                'order_id' => $orderId,
                'ingredients' => $ingredientsNeeded
            ]
        ]);
    }

    private function updateOrderStatus($orderId, $status)
    {
        return $this->client->post('http://gerente-web/api/orders/update-status', [
            'json' => [
                'order_id' => $orderId,
                'status' => $status
            ]
        ]);
    }

    public function getMenusWithIngredients()
    {
        try {
            $menus = Menu::with('ingredients')->get();
            return response()->json(['message' => 'Menus retrieved successfully', 'menus' => $menus], 200);
        } catch (Exception $e) {
            Log::error('Failed to retrieve menus', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve menus', 'error' => $e->getMessage()], 500);
        }
    }

    public function getRandomMenu()
    {
        try {
            $menu = Menu::inRandomOrder()->first();
            return response()->json(['menu' => $menu], 200);
        } catch (Exception $e) {
            Log::error('Failed to get random menu', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to get random menu', 'error' => $e->getMessage()], 500);
        }
    }
}

