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
        Schema::create('match_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->unsignedSmallInteger('possession')->nullable();
            $table->unsignedSmallInteger('shots')->nullable();
            $table->unsignedFloat('expected_goals', '3', '1')->nullable();
            $table->unsignedSmallInteger('passes')->nullable();
            $table->unsignedSmallInteger('tackles')->nullable();
            $table->unsignedSmallInteger('tackles_won')->nullable();
            $table->unsignedSmallInteger('interceptions')->nullable();
            $table->unsignedSmallInteger('saves')->nullable();
            $table->unsignedSmallInteger('fouls_committed')->nullable();
            $table->unsignedSmallInteger('offsides')->nullable();
            $table->unsignedSmallInteger('corners')->nullable();
            $table->unsignedSmallInteger('free_kicks')->nullable();
            $table->unsignedSmallInteger('penalty_kicks')->nullable();
            $table->unsignedSmallInteger('yellow_cards')->nullable();
            $table->unsignedSmallInteger('red_cards')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_statistics');
    }
};
