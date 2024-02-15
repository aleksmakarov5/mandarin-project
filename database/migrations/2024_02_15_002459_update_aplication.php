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
            $table->unsignedBigInteger('id_MRK');
            $table->unsignedBigInteger('id_spm');
            $table->unsignedBigInteger('mileage');
            $table->string('vehicle_passport');
            $table->string('Color');
            $table->string('ClientPasport');
            $table->date('DTPdate');
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
