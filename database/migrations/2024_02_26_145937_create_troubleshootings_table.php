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
        Schema::create('troubleshootings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('id_application');
            $table->string('PartName');
            $table->string('Work');
            $table->string('ExpertApinion')->nullable();
            $table->string('Сonclusion')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('troubleshootings');
    }
};
