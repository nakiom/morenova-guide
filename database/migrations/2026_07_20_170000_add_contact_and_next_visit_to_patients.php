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
            $table->string('email')->nullable()->after('age');
            $table->json('phones')->nullable()->after('email');
            $table->date('expected_next_visit_at')->nullable()->after('notes');
            $table->index('expected_next_visit_at');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['expected_next_visit_at']);
            $table->dropColumn(['email', 'phones', 'expected_next_visit_at']);
        });
    }
};
