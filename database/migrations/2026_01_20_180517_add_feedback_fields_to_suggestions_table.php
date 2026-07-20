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
        Schema::table('suggestions', function (Blueprint $table) {
            $table->foreignId('step_id')->nullable()->change();
            $table->foreignId('pathology_id')->nullable()->after('step_id')->constrained()->onDelete('cascade');
            $table->string('doctor_name')->nullable()->after('user_email');
            $table->string('treatment')->nullable()->after('doctor_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->dropForeign(['pathology_id']);
            $table->dropColumn(['pathology_id', 'doctor_name', 'treatment']);
            $table->foreignId('step_id')->nullable(false)->change();
        });
    }
};
