<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('session_number');
            $table->date('session_date');
            $table->unsignedInteger('shocks')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_sessions');
    }
};
