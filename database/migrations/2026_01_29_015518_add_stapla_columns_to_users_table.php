<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //追加カラム
            //アカウント状態
            $table->boolean('is_active')->default(true);
            $table->boolean('is_admin')->default(false);

            //最終ログイン
            $table->timestamp('last_login_at')->nullable();

            //通知時刻
            $table->time('email_morning_time')->nullable();
            $table->time('email_evening_time')->nullable();
            $table->time('line_morning_time')->nullable();
            $table->time('line_evening_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'is_admin',
                'last_login_at',
                'email_morning_time',
                'email_evening_time',
                'line_morning_time',
                'line_evening_time',
            ]);
        });
    }
};
