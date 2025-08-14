<?php

// 2025_03_07_000005_create_vtu_plans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVtuPlansTable extends Migration
{
    public function up()
    {
        Schema::create('vtu_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vtu_provider_id')->constrained()->onDelete('cascade');
            $table->string('network')->nullable(); // MTN, Airtel, etc.
            $table->string('type'); // airtime, data, tv, electricity
            $table->string('plan_code')->unique(); // e.g., MTN1GB, DSTV-Premium
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2); // Cost to user
            $table->decimal('commission', 10, 2)->default(0); // Admin/reseller commission
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vtu_plans');
    }
}