<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'ingredient_name',
        'quantity'
    ];

    protected $table = 'ingredient_menus';

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
