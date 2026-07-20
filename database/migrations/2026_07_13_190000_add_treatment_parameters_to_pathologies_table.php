<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pathologies', function (Blueprint $table) {
            $table->string('transducer', 50)->nullable()->after('background_image');
            $table->string('sessions_count', 30)->nullable()->after('transducer');
            $table->string('sessions_frequency', 60)->nullable()->after('sessions_count');
            $table->unsignedSmallInteger('session_duration_minutes')->nullable()->after('sessions_frequency');
            $table->unsignedSmallInteger('applications_per_session')->nullable()->after('session_duration_minutes');
            $table->unsignedSmallInteger('shots_per_application')->nullable()->after('applications_per_session');
            $table->unsignedInteger('shots_per_session')->nullable()->after('shots_per_application');
            $table->decimal('energy_kv', 4, 2)->nullable()->after('shots_per_session');
            $table->decimal('frequency_hz', 5, 2)->nullable()->after('energy_kv');
        });
    }

    public function down(): void
    {
        Schema::table('pathologies', function (Blueprint $table) {
            $table->dropColumn([
                'transducer',
                'sessions_count',
                'sessions_frequency',
                'session_duration_minutes',
                'applications_per_session',
                'shots_per_application',
                'shots_per_session',
                'energy_kv',
                'frequency_hz',
            ]);
        });
    }
};
