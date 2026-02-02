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
        Schema::create('todo', function (Blueprint $table) {
            //主キー(自動採番)
            $table->bigIncrements('todo_id');
            //資格計画ID(FK->study_plans.study_plans_id)
            $table->unsignedBigInteger('study_plans_id');
            $table->foreign('study_plans_id','fk_todo_sp')
            ->references('study_plans_id')
            ->on('study_plans')
            ->cascadeOnDelete();

            //日付(１日１レコード想定)
            $table->date('date');

            //備考
            $table->text('memo')->nullable();

            //作成日時
            $table->timestamps();

            //同一計画で同一日付を二重作成させない
            $table->unique(['study_plans_id','date'],'uq_todo_plan_date');

            //検索用インデックス
            $table->index('study_plans_id','idx_todo_plan');
            $table->index('date','idx_todo_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo');
    }
};
