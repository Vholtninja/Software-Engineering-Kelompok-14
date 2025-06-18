// database/migrations/xxxx_create_modules_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content');
            $table->string('video_url')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->integer('duration_minutes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};