<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Nullable foreign key
            $table->string('action'); // e.g., "rate_updated"
            $table->json('details'); // e.g., {"gift_card_id": 1, "old_rate": 0.80, "new_rate": 0.85}
            $table->timestamp('created_at'); // Only created_at
        });
    }

    public function down()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });
        Schema::dropIfExists('audit_logs');
    }
}