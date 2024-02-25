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
        //
        Schema::table('spare_parts', function (Blueprint $table) {
          $table->dropColumn('price_original_selected'); 
            $table->dropColumn('price_analogue_selected');
            $table->dropColumn('price_used_selected');
            $table->date('date_wait')->nullable();
            $table->string('price_ic_full')->nullable();
            $table->string('price_ic_half')->nullable();
            $table->string('selected_price')->nullable();
            $table->string('selected_mrk')->nullable();
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
