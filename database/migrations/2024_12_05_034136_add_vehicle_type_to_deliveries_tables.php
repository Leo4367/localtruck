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
            $table->bigInteger('vehicle_type_id')->after('dock_number')->nullable()->default(null);
        });

        Schema::table('pickups', function (Blueprint $table) {
            $table->bigInteger('vehicle_type_id')->after('dock_number')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('vehicle_type_id');
        });
        Schema::table('pickups', function (Blueprint $table) {
            $table->dropColumn('vehicle_type_id');
        });
    }
};
