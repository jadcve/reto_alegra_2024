<?php

namespace Tests\Unit;

use App\Events\OrderCreated;
use App\Http\Controllers\OrderController;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Status;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->withoutExceptionHandling();
    }

    public function testIndex()
    {
        $orders = Order::factory()->count(3)->create();

        $response = $this->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertViewHas('orders', function ($viewOrders) use ($orders) {
            return $viewOrders->pluck('id')->toArray() === $orders->pluck('id')->toArray();
        });
    }

    public function testStore()
    {
        $statusPending = Status::factory()->create(['name' => 'Pendiente']);
        $statusInProcess = Status::factory()->create(['name' => 'En proceso']);
        $orderData = ['quantity' => 5];

        $orderRequest = OrderRequest::create('/orders', 'POST', $orderData);
        $orderRequest->setContainer($this->app)->setRedirector($this->app->make('redirect'));

        $clientMock = Mockery::mock(Client::class);
        $clientMock->shouldReceive('post')->andReturn((object)['getStatusCode' => 202]);

        $this->app->instance(Client::class, $clientMock);

        $controller = new OrderController();
        $response = $controller->store($orderRequest);

        $this->assertDatabaseHas('orders', ['quantity' => 5, 'status_id' => $statusPending->id]);
        $order = Order::where('quantity', 5)->first();

        Event::assertDispatched(OrderCreated::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });

        $this->assertEquals($statusInProcess->id, $order->status_id);
        $response->assertRedirect(route('orders.index'));
    }

    public function testShow()
    {
        $order = Order::factory()->create();

        $response = $this->getJson(route('orders.show', $order->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'data' => ['id' => $order->id],
                 ]);
    }

    public function testUpdateStatus()
    {
        $order = Order::factory()->create();
        $status = Status::factory()->create(['name' => 'En proceso']);

        $request = Request::create('/orders/update-status', 'POST', [
            'order_id' => $order->id,
            'status' => 'En proceso',
        ]);

        $controller = new OrderController();
        $response = $controller->updateStatus($request);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Order status updated successfully']);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status_id' => $status->id]);
    }
}
