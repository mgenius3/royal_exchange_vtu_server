<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiptDataToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('vtu_orders', function (Blueprint $table) {
            $table->json('receipt_data')->nullable()->after('meta_data');
        });
    }

    public function down()
    {
        Schema::table('vtu_orders', function (Blueprint $table) {
            $table->dropColumn('receipt_data');
        });
    }
}