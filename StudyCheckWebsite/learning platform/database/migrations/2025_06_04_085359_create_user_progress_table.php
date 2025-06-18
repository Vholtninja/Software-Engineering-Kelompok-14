<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'course_id', 'module_id'], 'user_course_module_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};