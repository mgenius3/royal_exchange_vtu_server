<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('profit_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // 'admin' or 'developer'
            $table->double('balance')->default(0.00); // Use double for balance
            $table->timestamps();
        });

        // Seed initial wallet records for admin and developer
        DB::table('profit_wallets')->insert([
            ['type' => 'admin', 'balance' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'developer', 'balance' => 0.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('profit_wallets');
    }
};
