<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProofFileToCryptoTransactions extends Migration
{
    public function up()
    {
        Schema::table('crypto_transactions', function (Blueprint $table) {
            $table->string('proof_file')->nullable()->after('status'); // For sell transaction proof
        });
    }

    public function down()
    {
        Schema::table('crypto_transactions', function (Blueprint $table) {
            $table->dropColumn('proof_file');
        });
    }
}