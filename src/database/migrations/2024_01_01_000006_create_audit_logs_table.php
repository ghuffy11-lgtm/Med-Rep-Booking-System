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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('auditable_type')->comment('Model class name');
            $table->unsignedBigInteger('auditable_id')->comment('Model ID');
            $table->string('action', 100)->comment('created, updated, deleted, etc.');
            $table->json('old_values')->nullable()->comment('Before state');
            $table->json('new_values')->nullable()->comment('After state');
            $table->json('metadata')->nullable()->comment('Additional context');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index(['user_id', 'auditable_type', 'action', 'created_at'], 'idx_audit_lookup');
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
