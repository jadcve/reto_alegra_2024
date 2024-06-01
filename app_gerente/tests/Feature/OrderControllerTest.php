<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\Status;
use App\Events\OrderCreated;
use Illuminate\Support\Facades\Event;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;



    protected function setUp(): void
    {
        parent::setUp();

        config(['app.api_key' => '90b077b9-aa27-26719c-4b44-9814-f849ec1e0bfb.a3t6Xdm']);
    }

    /** @test */
    public function it_can_retrieve_all_orders()
    {
        $status = Status::create(['name' => 'Pendiente']);
        $order = Order::create([
            'quantity' => 5,
            'status_id' => $status->id,
        ]);

        $response = $this->getJson('/api/orders', [
            'x-api-key' => '90b077b9-aa27-26719c-4b44-9814-f849ec1e0bfb.a3t6Xdm'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         '*' => [
                             'id',
                             'quantity',
                             'status_id',
                             'created_at',
                             'updated_at',
                             'status' => [
                                 'id',
                                 'name',
                                 'created_at',
                                 'updated_at',
                             ]
                         ]
                     ],
                     'code'
                 ]);
    }

    /** @test */
    public function it_can_create_an_order()
    {
        Event::fake();

        $status = Status::create(['name' => 'Pendiente']);

        $response = $this->postJson('/api/orders/create', [
            'quantity' => 5,
        ], [
            'x-api-key' => '90b077b9-aa27-26719c-4b44-9814-f849ec1e0bfb.a3t6Xdm'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'Success',
                     'message' => 'Order created successfully',
                     'data' => [
                         'cantidad' => 5,
                         'status' => 'Pendiente',
                         'fecha' => true
                     ],
                     'code' => 201
                 ]);

        $this->assertDatabaseHas('orders', [
            'quantity' => 5,
            'status_id' => $status->id,
        ]);

        Event::assertDispatched(OrderCreated::class);
    }

    /** @test */
    public function it_can_retrieve_a_single_order()
    {
        $status = Status::create(['name' => 'Pendiente']);
        $order = Order::create([
            'quantity' => 5,
            'status_id' => $status->id,
        ]);

        $response = $this->getJson('/api/orders/' . $order->id, [
            'x-api-key' => '90b077b9-aa27-26719c-4b44-9814-f849ec1e0bfb.a3t6Xdm'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'Success',
                     'message' => 'Order retrieved successfully',
                     'data' => [
                         'id' => $order->id,
                         'quantity' => 5,
                         'status_id' => $status->id,
                         'created_at' => $order->created_at->toJSON(),
                         'updated_at' => $order->updated_at->toJSON(),
                         'status' => [
                             'id' => $status->id,
                             'name' => 'Pendiente',
                             'created_at' => $status->created_at->toJSON(),
                             'updated_at' => $status->updated_at->toJSON(),
                         ],
                     ],
                     'code' => 200
                 ]);
    }

    /** @test */
    public function it_fails_to_create_order_with_invalid_api_key()
    {
        $response = $this->postJson('/api/orders/create', [
            'quantity' => 5,
        ], [
            'x-api-key' => 'invalid_api_key'
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Acceso no autorizado',
                 ]);
    }

    /** @test */
    public function it_fails_to_retrieve_orders_with_invalid_api_key()
    {
        $response = $this->getJson('/api/orders', [
            'x-api-key' => 'invalid_api_key'
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Acceso no autorizado',
                 ]);
    }

    /** @test */
    public function it_fails_to_retrieve_single_order_with_invalid_api_key()
    {
        $status = Status::create(['name' => 'Pendiente']);
        $order = Order::create([
            'quantity' => 5,
            'status_id' => $status->id,
        ]);

        $response = $this->getJson('/api/orders/' . $order->id, [
            'x-api-key' => 'invalid_api_key'
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Acceso no autorizado',
                 ]);
    }
}
