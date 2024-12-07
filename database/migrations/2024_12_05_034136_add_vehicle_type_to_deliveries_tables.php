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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('vehicle_type')->after('dock_number')->nullable();
        });

        Schema::table('pickups', function (Blueprint $table) {
            $table->string('vehicle_type')->after('dock_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('vehicle_type');
        });
        Schema::table('pickups', function (Blueprint $table) {
            $table->dropColumn('vehicle_type');
        });
    }
};
