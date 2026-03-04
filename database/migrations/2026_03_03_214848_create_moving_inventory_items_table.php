<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('moving_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('inventory_item_id');
            $table->integer('warehouse_id');
            $table->integer('count');
            $table->integer('app_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('counterparty_id');
            $table->string('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moving_inventory_items');
    }
};
