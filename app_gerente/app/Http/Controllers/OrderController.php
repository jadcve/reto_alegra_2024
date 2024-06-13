<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Events\OrderCreated;
use App\Http\Requests\OrderRequest;
use App\Models\Status;
use App\Traits\ApiResponserTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    use ApiResponserTrait;

    public function index()
    {
        $orders = Order::with('status')->get();
        return view('orders.index', compact('orders'));
    }

    public function store(OrderRequest $request)
    {
        try {

            $statusPending = Status::where('name', 'Pendiente')->firstOrFail();
            $statusInProcess = Status::where('name', 'En proceso')->firstOrFail();

            // Crear la orden con estado "Pendiente"
            $order = Order::create([
                'quantity' => $request->quantity,
                'status_id' => $statusPending->id,
            ]);

            // Disparando el evento OrderCreated
            event(new OrderCreated($order));

            $order->status_id = $statusInProcess->id;
            $order->save();

            $client = new Client();
            $response = $client->post('http://cocina-web/api/prepare', [
                'json' => [
                    'order_id' => $order->id,
                    'quantity' => $order->quantity,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'x-api-key' => env('API_KEY'),
                ]
            ]);

            if ($response->getStatusCode() == 202) {
                return redirect()->route('orders.index')->with('info', 'Order created and sent to kitchen, waiting for ingredients.');
            } elseif ($response->getStatusCode() != 200) {
                throw new Exception('Failed to send order to kitchen');
            }

            return redirect()->route('orders.index')->with('success', 'Order created and sent to kitchen successfully');
        } catch (Exception $e) {
            Log::error('Failed to create order', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return redirect()->back()->with('error', 'Failed to create order');
        }
    }

    public function show($id)
    {
        try {
            $order = Order::with('status')->findOrFail($id);
            return $this->success('Order retrieved successfully', 200, $order);
        } catch (Exception $e) {
            Log::error('Failed to retrieve order', ['error' => $e->getMessage(), 'order_id' => $id]);
            return $this->error('Failed to retrieve order', 500, 'error');
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $statusName = $request->input('status');

            Log::info('Attempting to update order status', ['order_id' => $orderId, 'status' => $statusName]);

            $status = Status::where('name', $statusName)->firstOrFail();
            Log::info('Status found', ['status' => $status]);

            $order = Order::find($orderId);

            if (!$order) {
                Log::error('Order not found', ['order_id' => $orderId]);
                return response()->json(['message' => 'Order not found', 'error' => 'No query results for ' . $orderId], 404);
            }

            $order->status_id = $status->id;
            $order->save();

            Log::info('Order status updated successfully', ['order' => $order]);

            return response()->json(['message' => 'Order status updated successfully'], 200);
        } catch (Exception $e) {
            Log::error('Failed to update order status', ['error' => $e->getMessage(), 'order_id' => $orderId, 'status' => $statusName]);
            return response()->json(['message' => 'Failed to update order status', 'error'], 500);
        }
    }

    public function getIngredients()
    {
        $client = new Client();
        $response = $client->get('http://bodega-web/api/get-ingrediens', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'x-api-key' => env('API_KEY'),
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            return response()->json(['message' => 'Failed to retrieve ingredients', 'error' => (string) $response->getBody()], 400);
        }

        $ingredients = json_decode($response->getBody(), true);

        return response()->json(['message' => 'Ingredients retrieved successfully', 'ingredients' => $ingredients], 200);
    }

    public function showIngredients()
    {
        return view('ingredients');
    }

    public function testAngular()
    {
        return response()->json(['message' => 'Hello from Gerente']);
    }

}
