<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('center_id')->nullable()->after('is_admin')->constrained()->nullOnDelete();
            $table->foreignId('parent_user_id')->nullable()->after('center_id')->constrained('users')->nullOnDelete();
            $table->string('phone')->nullable()->after('parent_user_id');
            $table->string('location')->nullable()->after('phone');
            $table->string('job_title')->nullable()->after('location');
            $table->string('photo_path')->nullable()->after('job_title');
            $table->boolean('is_active')->default(true)->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('center_id');
            $table->dropConstrainedForeignId('parent_user_id');
            $table->dropColumn(['phone', 'location', 'job_title', 'photo_path', 'is_active']);
        });
    }
};
