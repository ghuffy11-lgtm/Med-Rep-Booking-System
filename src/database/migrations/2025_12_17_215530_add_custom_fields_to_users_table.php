<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add custom fields after 'password'
            $table->enum('role', ['super_admin', 'pharmacy_admin', 'representative'])
                  ->default('representative')
                  ->after('password');
            $table->string('company')->nullable()->after('role');
            $table->char('civil_id', 12)->unique()->nullable()->after('company');
            $table->boolean('is_active')->default(true)->after('civil_id');
            
            // Add indexes
            $table->index('role');
            $table->index('civil_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['civil_id']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['role', 'company', 'civil_id', 'is_active']);
        });
    }
};
