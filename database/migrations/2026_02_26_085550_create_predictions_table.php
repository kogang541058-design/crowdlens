<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * crowdlens/database/migrations/2026_02_26_085550_create_predictions_table.php
     */
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            
            // Features
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('call_type');
            $table->text('remarks'); 
            $table->date('report_date');
            $table->integer('report_day_of_week');
            
            // AI Output
            $table->integer('prediction_label');
            $table->float('confidence_score');
            
            // Human Feedback (The "Retraining" Data)
            $table->integer('actual_label')->nullable(); // 1 or 0
            $table->boolean('is_flagged_incorrect')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
