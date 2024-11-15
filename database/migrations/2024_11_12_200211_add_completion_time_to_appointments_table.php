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
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('completion_time')->after('time_slot')->nullable();
        });
        Schema::table('pickups', function (Blueprint $table) {
            $table->string('completion_time')->after('time_slot')->nullable();
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('completion_time')->after('time_slot')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('completion_time');
        });

        Schema::table('pickups', function (Blueprint $table) {
            $table->dropColumn('completion_time');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('completion_time');
        });
    }
};
