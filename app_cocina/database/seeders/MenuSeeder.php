<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\IngredientMenu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            ['name' => 'Pizza', 'description' => 'Delicious cheese pizza'],
            ['name' => 'Burger', 'description' => 'Juicy beef burger'],
            ['name' => 'Salad', 'description' => 'Fresh vegetable salad'],
            ['name' => 'Chicken Rice', 'description' => 'Tasty chicken rice'],
            ['name' => 'Lemon Potato', 'description' => 'Savory lemon potato'],
        ];

        $ingredients = [
            'Pizza' => [
                ['ingredient_name' => 'cheese', 'quantity' => 2],
                ['ingredient_name' => 'tomato', 'quantity' => 3],
                ['ingredient_name' => 'onion', 'quantity' => 1],
            ],
            'Burger' => [
                ['ingredient_name' => 'meat', 'quantity' => 1],
                ['ingredient_name' => 'lettuce', 'quantity' => 1],
                ['ingredient_name' => 'ketchup', 'quantity' => 1],
            ],
            'Salad' => [
                ['ingredient_name' => 'lettuce', 'quantity' => 2],
                ['ingredient_name' => 'tomato', 'quantity' => 1],
                ['ingredient_name' => 'lemon', 'quantity' => 1],
                ['ingredient_name' => 'chicken', 'quantity' => 1],
            ],
            'Chicken Rice' => [
                ['ingredient_name' => 'chicken', 'quantity' => 1],
                ['ingredient_name' => 'rice', 'quantity' => 1],
                ['ingredient_name' => 'onion', 'quantity' => 1],
            ],
            'Lemon Potato' => [
                ['ingredient_name' => 'lemon', 'quantity' => 1],
                ['ingredient_name' => 'potato', 'quantity' => 3],
                ['ingredient_name' => 'cheese', 'quantity' => 1],
            ],
        ];

        foreach ($menus as $menuData) {
            $menu = Menu::create($menuData);

            if (isset($ingredients[$menu->name])) {
                foreach ($ingredients[$menu->name] as $ingredient) {
                    IngredientMenu::create([
                        'menu_id' => $menu->id,
                        'ingredient_name' => $ingredient['ingredient_name'],
                        'quantity' => $ingredient['quantity']
                    ]);
                }
            }
        }
    }
}
