<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pathology_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('application_area_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_area_id');
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('application_area_id', 'app_area_fk')
                ->references('id')->on('application_areas')->cascadeOnDelete();
            $table->unique(['application_area_id', 'locale'], 'app_area_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_area_translations');
        Schema::dropIfExists('application_areas');
    }
};
