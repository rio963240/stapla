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
        Schema::create('backup_settings', function (Blueprint $table) {
            /**
             * 主キー（自動採番）
             */
            $table->bigIncrements('backup_settings_id');

            /**
             * 「1レコード運用」用キー（常に 'default' を入れる想定）
             * これを UNIQUE にして二重作成をDBで防ぐ
             */
            $table->string('settings_key', 50)->default('default')->unique();

            /**
             * 自動バックアップON/OFF
             */
            $table->boolean('is_enabled')->default(false);

            /**
             * 実行時刻（例：02:00）
             */
            $table->time('run_time')->default('02:00:00');

            /**
             * 実行頻度（例：daily / weekly）
             * まずはシンプルに文字列で保持（拡張しやすい）
             */
            $table->string('frequency', 20)->default('daily');

            /**
             * 保持する世代数（例：直近7世代）
             */
            $table->unsignedSmallInteger('retention_count')->default(7);

            /**
             * 作成日時・更新日時（設定変更履歴として意味がある）
             */
            $table->timestamps();

            /**
             * 検索用（管理画面での検索はほぼないが保険）
             */
            $table->index('is_enabled', 'idx_backup_settings_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};
