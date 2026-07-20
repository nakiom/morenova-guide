<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pathology_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('follow_up_item_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('follow_up_item_id');
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('follow_up_item_id', 'follow_up_item_fk')
                ->references('id')->on('follow_up_items')->onDelete('cascade');
            $table->unique(['follow_up_item_id', 'locale'], 'follow_up_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up_item_translations');
        Schema::dropIfExists('follow_up_items');
    }
};
