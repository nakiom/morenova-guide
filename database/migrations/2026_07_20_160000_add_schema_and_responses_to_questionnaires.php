<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->json('schema')->nullable()->after('max_score');
            $table->string('description')->nullable()->after('name');
        });

        Schema::table('questionnaire_scores', function (Blueprint $table) {
            $table->json('responses')->nullable()->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->dropColumn(['schema', 'description']);
        });

        Schema::table('questionnaire_scores', function (Blueprint $table) {
            $table->dropColumn('responses');
        });
    }
};
