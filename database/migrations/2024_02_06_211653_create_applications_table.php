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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('id_insuranceCompany');
            $table->unsignedInteger('numberInsuranceCompanyApplication');
            $table->string('CarModel');
            $table->string('gnz')->nullable();
            $table->string('vin')->nulable();
            $table->string('ClientName')->nulable();
            $table->string('ClientPhone')->nulable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
