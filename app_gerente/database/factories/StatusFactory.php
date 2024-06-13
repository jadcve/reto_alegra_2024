<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition()
    {
        static $statusNames = ['Pendiente', 'En proceso', 'Despachada'];
        static $index = 0;

        return [
            'name' => $statusNames[$index++ % count($statusNames)],
        ];
    }
}
