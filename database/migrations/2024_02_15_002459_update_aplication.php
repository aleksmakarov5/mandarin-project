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
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedBigInteger('id_MRK')->nullable();
            $table->unsignedBigInteger('id_spm')->nullable();
            $table->unsignedBigInteger('mileage')->nullable();
            $table->string('vehicle_passport')->nullable();
            $table->string('Color')->nullable();
            $table->string('ClientPasport')->nullable();
            $table->date('DTPdate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
