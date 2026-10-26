<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionnaire_scores', function (Blueprint $table) {
            $table->text('patient_message')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaire_scores', function (Blueprint $table) {
            $table->dropColumn('patient_message');
        });
    }
};
