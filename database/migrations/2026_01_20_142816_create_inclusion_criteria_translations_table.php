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
        Schema::create('inclusion_criteria_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inclusion_criteria_id')->constrained('inclusion_criteria')->onDelete('cascade');
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();
            
            $table->unique(['inclusion_criteria_id', 'locale'], 'inc_crit_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inclusion_criteria_translations');
    }
};
