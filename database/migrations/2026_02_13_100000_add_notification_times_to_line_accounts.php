<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('line_accounts', function (Blueprint $table) {
            $table->string('notification_morning_at', 5)->default('07:00');
            $table->string('notification_evening_at', 5)->default('21:00');
        });
    }

    public function down(): void
    {
        Schema::table('line_accounts', function (Blueprint $table) {
            $table->dropColumn(['notification_morning_at', 'notification_evening_at']);
        });
    }
};
