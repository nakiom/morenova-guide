<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exclusion_criteria_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exclusion_criteria_id')->constrained('exclusion_criteria')->onDelete('cascade');
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();
            
            $table->unique(['exclusion_criteria_id', 'locale'], 'exc_crit_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exclusion_criteria_translations');
    }
};
