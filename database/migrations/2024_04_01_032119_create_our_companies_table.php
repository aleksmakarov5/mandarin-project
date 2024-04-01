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
        Schema::create('our_companies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('our_name');
            $table->string('our_email');
            $table->string('our_mailer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('our_companies');
    }
};
