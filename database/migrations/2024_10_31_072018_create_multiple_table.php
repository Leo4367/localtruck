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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->string('pickup_number');
            $table->string('driver_name');
            $table->string('time_slot');
            $table->bigInteger('warehouse_id');
            $table->string('type');
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('description')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('pickups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointments_id')->constrained()->onDelete('cascade');
            $table->string('pickup_number');
            $table->string('driver_name');
            $table->string('phone_number');
            $table->string('time_slot');
            $table->bigInteger('warehouse_id');
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointments_id')->constrained()->onDelete('cascade');
            $table->string('container_number');
            $table->string('driver_name');
            $table->string('phone_number');
            $table->string('time_slot');
            $table->bigInteger('warehouse_id');
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('date_manages', function (Blueprint $table) {
            $table->id();
            $table->string('forbidden_date');
            $table->bigInteger('warehouse_id');
            $table->string('type');
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('all_time_slots', function (Blueprint $table) {
            $table->id();
            $table->datetime('time_slot');
            $table->string('date_slot');
            $table->bigInteger('warehouse_id');
            $table->string('type');
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('pickups');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('date_manages');
        Schema::dropIfExists('all_time_slots');
    }
};
