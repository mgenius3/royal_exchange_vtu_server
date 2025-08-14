<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRangesToGiftCardsTable extends Migration
{
public function up()
{
Schema::table('gift_cards', function (Blueprint $table) {
$table->json('ranges')->nullable()->after('stock');
});
}

public function down()
{
Schema::table('gift_cards', function (Blueprint $table) {
$table->dropColumn('ranges');
});
}
}