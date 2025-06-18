// database/migrations/xxxx_create_homework_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('question');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('subject', ['math', 'science', 'english', 'history', 'other']);
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->json('attachments')->nullable();
            $table->enum('status', ['pending', 'answered', 'closed'])->default('pending');
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework');
    }
};