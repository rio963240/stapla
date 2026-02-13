<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * LINE通知時間は users.line_morning_time / line_evening_time に統一するため、
     * line_accounts から削除する。
     */
    public function up(): void
    {
        if (!Schema::hasTable('line_accounts')) {
            return;
        }
        Schema::table('line_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('line_accounts', 'notification_morning_at')) {
                $table->dropColumn(['notification_morning_at', 'notification_evening_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('line_accounts', function (Blueprint $table) {
            $table->string('notification_morning_at', 5)->default('07:00');
            $table->string('notification_evening_at', 5)->default('21:00');
        });
    }
};
