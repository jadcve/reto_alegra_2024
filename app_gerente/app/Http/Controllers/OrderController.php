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
            Log::info('llegando al controlador');
            $statusPending = Status::where('name', 'Pendiente')->firstOrFail();
            $statusInProcess = Status::where('name', 'En proceso')->firstOrFail();

            $order = Order::create([
                'quantity' => $request->quantity,
                'status_id' => $statusPending->id,
            ]);

            event(new OrderCreated($order));

            $order->status_id = $statusInProcess->id;
            $order->save();

            $client = new Client();
            $response = $client->post('http://cocina-web/api/prepare', [
                'json' => [
                    'order_id' => $order->id,
                    'quantity' => $order->quantity,
                ]
            ]);

            if ($response->getStatusCode() == 202) {
                return $this->success('Order created and sent to kitchen, waiting for ingredients.', 201, [
                "cantidad" => $order->quantity,
                "status" => $order->getStatusNameAttribute(),
                "fecha" => $order->created_at
            ]);


                // return redirect()->route('orders.index')->with('info', 'Order created and sent to kitchen, waiting for ingredients.');
            } elseif ($response->getStatusCode() != 200) {
                return $this->error('Ah ocurrido un error', 500);

                //throw new Exception('Failed to send order to kitchen');
            }

            return $this->success('Order created and sent to kitchen successfully', 201, [
                "cantidad" => $order->quantity,
                "status" => $order->getStatusNameAttribute(),
                "fecha" => $order->created_at
            ]);

            // return redirect()->route('orders.index')->with('success', 'Order created and sent to kitchen successfully');
        } catch (Exception $e) {
            Log::error('Failed to create order', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return $this->error('Failed to create order', 500);
            // return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $order = Order::with('status')->findOrFail($id);
            return $this->success('Order retrieved successfully', 200, $order);
        } catch (Exception $e) {
            Log::error('Failed to retrieve order', ['error' => $e->getMessage(), 'order_id' => $id]);
            return $this->error('Failed to retrieve order', 500, $e->getMessage());
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
                return response()->json(['message' => 'Order not found', 'error' => 'No query results for model [App\Models\Order] ' . $orderId], 404);
            }

            $order->status_id = $status->id;
            $order->save();

            Log::info('Order status updated successfully', ['order' => $order]);

            return response()->json(['message' => 'Order status updated successfully'], 200);
        } catch (Exception $e) {
            Log::error('Failed to update order status', ['error' => $e->getMessage(), 'order_id' => $orderId, 'status' => $statusName]);
            return response()->json(['message' => 'Failed to update order status', 'error' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('orders.create');
    }
}
