<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questionnaire_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phase', 20);
            $table->decimal('score', 8, 2);
            $table->date('scored_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'questionnaire_id', 'scored_at']);
            $table->index(['patient_id', 'phase']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_scores');
    }
};
