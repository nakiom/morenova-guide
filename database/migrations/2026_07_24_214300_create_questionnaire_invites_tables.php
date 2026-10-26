<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('phase', 20);
            $table->json('questionnaire_ids');
            $table->string('email');
            $table->timestamp('expires_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('questionnaire_invite_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_invite_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questionnaire_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questionnaire_score_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['questionnaire_invite_id', 'questionnaire_id'], 'invite_questionnaire_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_invite_items');
        Schema::dropIfExists('questionnaire_invites');
    }
};
