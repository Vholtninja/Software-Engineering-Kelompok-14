// database/migrations/xxxx_create_courses_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('slug')->unique();
            $table->string('category');
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->string('thumbnail')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};