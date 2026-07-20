<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pathology_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('session_checklist_item_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_checklist_item_id');
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();

            $table->foreign('session_checklist_item_id', 'checklist_item_fk')
                ->references('id')->on('session_checklist_items')->onDelete('cascade');
            $table->unique(['session_checklist_item_id', 'locale'], 'checklist_item_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_checklist_item_translations');
        Schema::dropIfExists('session_checklist_items');
    }
};
