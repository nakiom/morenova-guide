<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('mailable')->nullable();
            $table->string('type', 60)->default('generic');
            $table->string('to_email');
            $table->string('subject')->nullable();
            $table->string('status', 20)->default('sent'); // sent|failed
            $table->string('mailer', 40)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('questionnaire_invite_id')->nullable()->constrained()->nullOnDelete();
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('sent_at');
        });

        Schema::table('questionnaire_invites', function (Blueprint $table) {
            $table->timestamp('visited_at')->nullable()->after('completed_at');
            $table->unsignedInteger('visit_count')->default(0)->after('visited_at');
            $table->unsignedInteger('sent_count')->default(0)->after('visit_count');
            $table->timestamp('last_sent_at')->nullable()->after('sent_count');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaire_invites', function (Blueprint $table) {
            $table->dropColumn(['visited_at', 'visit_count', 'sent_count', 'last_sent_at']);
        });

        Schema::dropIfExists('mail_logs');
        Schema::dropIfExists('settings');
    }
};
