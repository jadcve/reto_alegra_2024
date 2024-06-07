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

            // Crear la orden con estado "Pendiente"
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

            if ($response->getStatusCode() != 200) {
                throw new Exception('Failed to send order to kitchen');
            }

            return redirect()->route('orders.index')->with('success', 'Order created and sent to kitchen successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage());
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

    public function updateStatus(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $statusName = $request->input('status');

            $status = Status::where('name', $statusName)->firstOrFail();
            $order = Order::findOrFail($orderId);
            $order->status_id = $status->id;
            $order->save();

            return response()->json(['message' => 'Order status updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update order status', 'error' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('orders.create');
    }
}
