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
        Schema::create('chat_recs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('id_to');
            $table->unsignedInteger('id_chat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_recs');
    }
};
