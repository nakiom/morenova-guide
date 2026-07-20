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
        Schema::create('step_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description');
            $table->timestamps();
            
            $table->unique(['step_id', 'locale'], 'step_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('step_translations');
    }
};
