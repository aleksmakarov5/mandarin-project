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
        Schema::create('telegram_actions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('id_chat');
            $table->unsignedInteger('id_application');
            $table->unsignedInteger('action');
            $table->unsignedInteger('number');
            $table->unsignedInteger('id_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_actions');
    }
};
