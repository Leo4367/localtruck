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
        // 删除 pickups 表中的外键约束
        Schema::table('pickups', function (Blueprint $table) {
            $table->dropForeign(['appointments_id']); // 假设外键列名为 appointments_id
        });

        // 删除 deliveries 表中的外键约束
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['appointments_id']); // 假设外键列名为 appointments_id
        });
        Schema::dropIfExists('appointments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*// 恢复 pickups 表的外键约束
        Schema::table('pickups', function (Blueprint $table) {
            $table->foreign('appointments_id')->references('id')->on('appointments')->onDelete('cascade');
        });

        // 恢复 deliveries 表的外键约束
        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreign('appointments_id')->references('id')->on('appointments')->onDelete('cascade');
        });*/
    }
};
