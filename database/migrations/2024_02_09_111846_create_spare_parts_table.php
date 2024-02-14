<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('part_number');
            $table->string('order_number');
            $table->string('price_original');
            $table->boolean('price_original_selected');
            $table->string('price_analogue');
            $table->boolean('price_analogue_selected');
            $table->string('price_used');
            $table->boolean('price_used_selected');
            $table->date('date_price');
            $table->string('status');
            $table->unsignedBigInteger('id_application');
            $table->string('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
