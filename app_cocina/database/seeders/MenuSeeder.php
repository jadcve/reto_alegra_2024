<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Ingredient;

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

        foreach ($menus as $menuData) {
            $menu = Menu::create($menuData);

            if ($menu->name == 'Pizza') {
                $menu->ingredients()->attach(Ingredient::where('name', 'cheese')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'tomato')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'onion')->first());
            } elseif ($menu->name == 'Burger') {
                $menu->ingredients()->attach(Ingredient::where('name', 'meat')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'lettuce')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'ketchup')->first());
            } elseif ($menu->name == 'Salad') {
                $menu->ingredients()->attach(Ingredient::where('name', 'lettuce')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'tomato')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'lemon')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'chicken')->first());
            } elseif ($menu->name == 'Chicken Rice') {
                $menu->ingredients()->attach(Ingredient::where('name', 'chicken')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'rice')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'onion')->first());
            } elseif ($menu->name == 'Lemon Potato') {
                $menu->ingredients()->attach(Ingredient::where('name', 'lemon')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'potato')->first());
                $menu->ingredients()->attach(Ingredient::where('name', 'cheese')->first());
            }
        }

        $ingredients = Ingredient::all();
        foreach ($ingredients as $ingredient) {
            $used = false;
            foreach ($menus as $menuData) {
                $menu = Menu::where('name', $menuData['name'])->first();
                if ($menu->ingredients->contains($ingredient)) {
                    $used = true;
                    break;
                }
            }

            if (!$used) {
                $menu = Menu::inRandomOrder()->first();
                $menu->ingredients()->attach($ingredient);
            }
        }
    }
}
