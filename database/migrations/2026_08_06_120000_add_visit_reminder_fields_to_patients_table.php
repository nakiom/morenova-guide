<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('visit_reminder_enabled')->default(false)->after('expected_next_visit_at');
            $table->unsignedTinyInteger('visit_reminder_days_before')->nullable()->after('visit_reminder_enabled');
            $table->date('visit_reminder_sent_for')->nullable()->after('visit_reminder_days_before');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'visit_reminder_enabled',
                'visit_reminder_days_before',
                'visit_reminder_sent_for',
            ]);
        });
    }
};
