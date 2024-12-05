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
        Schema::create('purchasers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('address');
            $table->string('work_order');
            $table->timestamps();
        });

        Schema::create('brokers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name'); // 公司名称
            $table->string('broker_name'); // 中间人的名称
            $table->string('email'); // 联系邮箱
            $table->timestamps();
        });

        Schema::create('inquiry_price', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchaser_id')->constrained('purchasers')->onDelete('cascade'); // 关联 works
            $table->foreignId('broker_id')->constrained('brokers')->onDelete('cascade'); // 关联 broker
            $table->float('price')->nullable(); // 报价金额
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiry_price', function (Blueprint $table) {
            $table->dropForeign(['purchaser_id']);
            $table->dropForeign(['broker_id']);
        });
        Schema::dropIfExists('purchasers');
        Schema::dropIfExists('brokers');
        Schema::dropIfExists('inquiry_price');
    }
};
