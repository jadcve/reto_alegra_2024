<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Events\OrderCreated;
use App\Http\Requests\OrderRequest;
use App\Models\Status;
use App\Traits\ApiResponserTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    use ApiResponserTrait;

    public function index()
    {

        try {
            $orders = Order::with('status')->get();
            return $this->success('Orders retrieved successfully', 200, $orders);
        } catch (Exception $e) {
            return $this->error('Failed to retrieve orders', 500, $e->getMessage());
        }
    }

    public function store(OrderRequest $request)
    {
        try {
            $status = Status::where('name', 'Pendiente')->firstOrFail();

            $order = Order::create([
                'quantity' => $request->quantity,
                'status_id' => $status->id,
            ]);

            event(new OrderCreated($order));

            // Enviar la solicitud al microservicio cocina
            Log::info("Estoy a punto de salir a cocina");
            $client = new Client();
            $response = $client->post('http://cocina-app:8082/api/prepare', [
                'json' => [
                    'order_id' => $order->id,
                    'quantity' => $order->quantity,
                ]
            ]);

            if ($response->getStatusCode() != 200) {
                throw new Exception('Failed to send order to kitchen');
            }

            return $this->success('Order created and sent to kitchen successfully', 201, [
                "cantidad" => $order->quantity,
                "status" => $order->getStatusNameAttribute(),
                "fecha" => $order->created_at
            ]);
        } catch (Exception $e) {
            return $this->error('Failed to create order', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $order = Order::with('status')->findOrFail($id);
            return $this->success('Order retrieved successfully', 200, $order);
        } catch (Exception $e) {
            return $this->error('Failed to retrieve order', 500, $e->getMessage());
        }
    }
}
