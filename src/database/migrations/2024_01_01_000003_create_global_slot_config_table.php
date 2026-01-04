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
        Schema::create('global_slot_config', function (Blueprint $table) {
            $table->id();
            $table->time('non_pharmacy_start_time')->default('13:00:00');
            $table->time('non_pharmacy_end_time')->default('16:00:00');
            $table->time('pharmacy_start_time')->default('13:00:00');
            $table->time('pharmacy_end_time')->default('14:40:00');
            $table->integer('slot_duration_minutes')->default(10);
            $table->json('allowed_days')->comment('["Tuesday", "Thursday"]');
            $table->integer('non_pharmacy_daily_limit')->default(20);
            $table->integer('pharmacy_daily_limit')->default(10);
            $table->integer('cooldown_days')->default(14)->comment('2 weeks');
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Index
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_slot_config');
    }
};
