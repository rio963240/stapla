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
        Schema::create('backup_files', function (Blueprint $table) {
            /**
             * 主キー（自動採番）
             */
            $table->bigIncrements('backup_files_id');

            /**
             * 自動 or 手動バックアップ
             * true = 自動 / false = 手動
             */
            $table->boolean('is_auto')->default(true);

            /**
             * ファイル名
             */
            $table->string('file_name', 255);

            /**
             * ファイルパス
             */
            $table->string('file_path', 255);

            /**
             * 成功 or 失敗
             * true = 成功 / false = 失敗
             */
            $table->boolean('is_success')->default(true);

            /**
             * ファイルサイズ（バイト）
             */
            $table->unsignedBigInteger('size');

            /**
             * 作成日時（バックアップ実行日時）
             */
            $table->timestamp('created_at')->useCurrent();

            /**
             * 検索用インデックス
             */
            $table->index('created_at', 'idx_backup_files_created_at');
            $table->index('is_auto', 'idx_backup_files_is_auto');
            $table->index('is_success', 'idx_backup_files_is_success');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_files');
    }
};
