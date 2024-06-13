<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'quantity' => $this->faker->numberBetween(1, 100),
            'status_id' => Status::factory(),
        ];
    }
}
