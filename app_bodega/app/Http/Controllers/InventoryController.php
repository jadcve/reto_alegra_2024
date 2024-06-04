<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function requestIngredients(Request $request)
    {
        try {
            Log::info("llegue a bodega");
            $ingredientsNeeded = $request->input('ingredients');
            $notAvailable = [];

            foreach ($ingredientsNeeded as $ingredientName => $quantity) {
                $ingredient = Ingredient::where('name', $ingredientName)->first();

                if ($ingredient->quantity < $quantity) {
                    // Intentar comprar más ingredientes
                    $response = Http::post('https://recruitment.alegra.com/api/farmers-market/buy', [
                        'ingredient' => $ingredientName
                    ]);

                    if ($response->successful() && $response->json('quantitySold') > 0) {
                        $ingredient->quantity += $response->json('quantitySold');
                        $ingredient->save();
                    } else {
                        $notAvailable[$ingredientName] = $quantity;
                    }
                }

                if ($ingredient->quantity < $quantity) {
                    $notAvailable[$ingredientName] = $quantity;
                } else {
                    $ingredient->quantity -= $quantity;
                    $ingredient->save();
                }
            }

            if (!empty($notAvailable)) {
                return response()->json(['message' => 'Some ingredients are not available', 'not_available' => $notAvailable], 400);
            }

            return response()->json(['message' => 'Ingredients provided successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to process ingredient request', 'error' => $e->getMessage()], 500);
        }
    }
}
