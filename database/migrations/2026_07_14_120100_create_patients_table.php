<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pathology_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('external_id')->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('sessions_count')->default(0);
            $table->unsignedInteger('total_shocks')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'pathology_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
