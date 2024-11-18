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
        Schema::table('pickups', function (Blueprint $table) {
            $table->dropColumn('appointments_id');
            $table->bigInteger('user_id')->after('id');
            $table->string('po_number')->after('phone_number');
            $table->renameColumn('pickup_number', 'appt_number');
            $table->string('dock_number')->after('warehouse_id');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('appointments_id');
            $table->bigInteger('user_id')->after('id');
            $table->string('po_number')->after('phone_number');
            $table->renameColumn('container_number', 'appt_number');
            $table->string('dock_number')->after('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickups', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('po_number');
            $table->renameColumn('appt_number', 'pickup_number');
            $table->dropColumn('dock_number');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('po_number');
            $table->renameColumn('appt_number', 'container_number');
            $table->dropColumn('dock_number');
        });
    }
};
