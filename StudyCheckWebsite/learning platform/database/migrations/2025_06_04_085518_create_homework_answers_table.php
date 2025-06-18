// database/migrations/xxxx_create_homework_answers_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homework_answers', function (Blueprint $table) {
            $table->id();
            $table->text('answer');
            $table->text('explanation')->nullable();
            $table->foreignId('homework_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->json('attachments')->nullable();
            $table->boolean('is_best_answer')->default(false);
            $table->integer('upvotes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_answers');
    }
};