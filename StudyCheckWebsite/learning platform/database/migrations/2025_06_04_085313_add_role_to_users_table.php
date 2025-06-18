// database/migrations/xxxx_add_role_to_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'teacher', 'expert', 'moderator', 'admin'])
                  ->default('student')->after('email');
            $table->string('avatar')->nullable()->after('role');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('institution')->nullable()->after('bio');
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])
                  ->default('beginner')->after('institution');
            $table->boolean('is_verified')->default(false)->after('level');
            $table->integer('points')->default(0)->after('is_verified');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'avatar', 'bio', 'institution', 'level', 'is_verified', 'points']);
        });
    }
};