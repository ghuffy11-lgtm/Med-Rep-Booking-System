<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add mobile_number field after civil_id
            $table->string('mobile_number', 20)->unique()->nullable()->after('civil_id');

            // Add index for mobile_number
            $table->index('mobile_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['mobile_number']);
            $table->dropColumn('mobile_number');
        });
    }
};
