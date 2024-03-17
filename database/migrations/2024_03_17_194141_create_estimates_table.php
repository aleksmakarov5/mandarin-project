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
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('id_application');
            $table->string('price_sp_full')->nullable();
            $table->string('price_sp_half')->nullable();
            $table->string('price_repair')->nullable();
            $table->string('price_paint')->nullable();
            $table->string('price_zip')->nullable();
            $table->string('price_work')->nullable();
            $table->string('price_repair_full')->nullable();
            $table->string('price_repair_half')->nullable();
            $table->string('dog_number')->nullable();
            $table->string('zn_number')->nullable();
            $table->integer('hda_number')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
