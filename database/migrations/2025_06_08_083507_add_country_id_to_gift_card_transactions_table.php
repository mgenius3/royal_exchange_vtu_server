<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('user_id');
    
            // Optional: if you want to enforce foreign key
            // $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            // $table->dropForeign(['country_id']); // if foreign key was added
            $table->dropColumn('country_id');
        });
    }
    
};