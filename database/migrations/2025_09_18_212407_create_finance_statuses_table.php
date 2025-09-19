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
        Schema::create('finance_statuses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('FinStatusName');
            $table->integer('fin_time')->nullable();
            $table->integer('fin_mrk_check')->nullable();
            $table->integer('fin_km_check')->nullable();
            $table->boolean('fin_day')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_statuses');
    }
};