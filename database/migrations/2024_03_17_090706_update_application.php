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
        Schema::table(
            'applications',
            function (Blueprint $table) {
                $table->string('comment_true')->nullable();
                $table->string('comment_false')->nullable();
                $table->string('ClientPasport_number')->nullable();
                $table->date('ClientPasport_date')->nullable();
                $table->string('ClientPasport_struct')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('wait_sp')->nullable();
                $table->unsignedBigInteger('def')->default(0);
                $table->string('dogovor')->nullable();
                $table->string('mnf_year')->nullable();
                $table->string('ub_number')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
