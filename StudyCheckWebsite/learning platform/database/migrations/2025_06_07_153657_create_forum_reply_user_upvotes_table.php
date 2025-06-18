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
        Schema::create('forum_reply_user_upvotes', function (Blueprint $table) {
            $table->primary(['user_id', 'forum_reply_id']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('forum_reply_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_reply_user_upvotes');
    }
};