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
        Schema::create('study_records', function (Blueprint $table) {
            /**
             * 主キー（自動採番）
             */
            $table->bigIncrements('study_records_id');

            /**
             * ToDoリストID（FK → todo.todo_id）
             */
            $table->unsignedBigInteger('todo_id');
            $table->foreign('todo_id', 'fk_sr_todo')
                ->references('todo_id')
                ->on('todo')
                ->cascadeOnDelete();

            /**
             * 個別ToDoID（FK → study_plan_items.study_plan_items_id）
             */
            $table->unsignedBigInteger('study_plan_items_id');
            $table->foreign('study_plan_items_id', 'fk_sr_spi')
                ->references('study_plan_items_id')
                ->on('study_plan_items')
                ->cascadeOnDelete();

            /**
             * 実勉強時間（分）
             */
            $table->unsignedSmallInteger('actual_minutes');

            /**
             * 完了/未完了（定義書: default FALSE）
             */
            $table->boolean('is_completed')->default(false);

            /**
             * 作成日時・更新日時
             */
            $table->timestamps();

            /**
             * 検索用インデックス（実務で効く）
             */
            $table->index('todo_id', 'idx_sr_todo');
            $table->index('study_plan_items_id', 'idx_sr_spi');

            /**
             * 任意：1個別ToDoにつき実績は1件だけ、にしたい場合は有効化
             * （「上書き更新」運用ならこれが自然）
             */
            // $table->unique('study_plan_items_id', 'uq_sr_spi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_records');
    }
};
