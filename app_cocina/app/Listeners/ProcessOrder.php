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

        $order = $event->order;

        $statusInProcess = Status::where('name', 'En proceso')->firstOrFail();
        $order->status_id = $statusInProcess->id;
        $order->save();

        sleep(10);

        $statusDispatched = Status::where('name', 'Despachada')->firstOrFail();
        $order->status_id = $statusDispatched->id;
        $order->save();
    }
}
