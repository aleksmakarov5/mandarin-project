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
        Schema::create('status_applications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_application');
            $table->boolean('is_readed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_applications');
    }
};