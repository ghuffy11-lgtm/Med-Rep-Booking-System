<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false);
            $table->json('override_days')->nullable()->comment('Optional day overrides');
            $table->text('notes')->nullable()->comment('Closure reason or notes');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['department_id', 'start_date', 'end_date']);
            $table->index('is_active');
            
            // NOTE: Check constraint removed (not supported in Laravel 10 migration syntax)
            // Validation will be handled at application level
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
