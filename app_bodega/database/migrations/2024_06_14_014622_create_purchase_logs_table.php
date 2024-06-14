<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseLogsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ingredient_name');
            $table->integer('quantity_sold');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_logs');
    }
};
