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
        Schema::create('mrk_filters', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_mrk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mrk_filters');
    }
};
