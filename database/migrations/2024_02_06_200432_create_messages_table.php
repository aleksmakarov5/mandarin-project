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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mailbox_id');
            $table->unsignedBigInteger('uid');
            $table->string('subject')->nullable();
            $table->text('body_text')->nullable();
            $table->text('body_html')->nullable();
            $table->unsignedBigInteger('attachment_count')->default(0);
            $table->text('header')->nullable();
            $table->dateTime('message_date')->nullable();
            $table->dateTime('create_date');
            $table->dateTime('modify_date')->nullable();
            $table->boolean('is_ready');
            $table->boolean('is_parsed');
            $table->unsignedBigInteger('application_id');
            // $table->foreign('mailbox_id')->references('id')->on('mailboxes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
