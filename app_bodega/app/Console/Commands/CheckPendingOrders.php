<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\InventoryController;

class CheckPendingOrders extends Command
{
    protected $signature = 'orders:check-pending';
    protected $description = 'Revisión de ordenes pendientes y actualización de inventario.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $bodegaController = new InventoryController();
        $bodegaController->checkPendingOrders();
        $this->info('Ordenes pendientes revisadas y actualizadas.');
    }
}
