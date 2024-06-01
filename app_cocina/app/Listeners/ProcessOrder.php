<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Status;

class ProcessOrder implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        // Aquí iría la lógica para procesar la orden
        // Por ejemplo, cambiar el estado de la orden a "En proceso" y luego a "Despachada"
        $order = $event->order;

        $statusInProcess = Status::where('name', 'En proceso')->firstOrFail();
        $order->status_id = $statusInProcess->id;
        $order->save();

        // Simular el procesamiento de la orden
        sleep(10);

        $statusDispatched = Status::where('name', 'Despachada')->firstOrFail();
        $order->status_id = $statusDispatched->id;
        $order->save();
    }
}
