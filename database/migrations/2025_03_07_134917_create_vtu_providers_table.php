<?php

// 2025_03_07_000004_create_vtu_providers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVtuProvidersTable extends Migration
{
    public function up()
    {
        Schema::create('vtu_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., VTpass, Mobilevtu
            $table->string('api_key')->nullable();
            $table->string('api_token')->nullable();
            $table->string('base_url')->nullable(); // API endpoint
            $table->boolean('is_active')->default(true);
            $table->integer('success_rate')->default(100); // Percentage
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vtu_providers');
    }
}