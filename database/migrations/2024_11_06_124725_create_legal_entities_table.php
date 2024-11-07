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
        Schema::create('legal_entities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nickname');
            $table->string('name');
            $table->string('fullname');
            $table->string('in_face')->nullable();
            $table->string('type');
            $table->string('director');
            $table->string('bank');
            $table->string('kpp');
            $table->string('bik');
            $table->string('okpo');
            $table->string('sch_korr');
            $table->string('sch_numb');
            $table->string('inn');
            $table->string('ogrn');
            $table->string('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_entities');
    }
};
