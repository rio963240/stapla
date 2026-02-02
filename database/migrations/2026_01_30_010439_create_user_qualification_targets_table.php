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
        Schema::create('user_qualification_targets', function (Blueprint $table) {
            //主キー（自動採番）
            $table->bigIncrements('user_qualification_targets_id');

            //ユーザーID(FK->users.user_id)
            $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnDelete();

            //資格ID(FK->qualifications.qualification_id)
            $table->unsignedBigInteger('qualification_id');
            $table->foreign('qualification_id')
            ->references('qualification_id')
            ->on('qualification')
            ->onDelete('restrict');

            //勉強開始日/試験日
            $table->date('start_date');
            $table->date('exam_date');

            //１日に学習時間(０〜９９９分想定)
            $table->unsignedSmallInteger('daily_study_time');

            //バッファ率(０〜９９％想定)
            $table->unsignedTinyInteger('buffer_rate');

            //作成日時
            $table->timestamps();

            //検索・JOIN用のインデックス
            $table->index(['user_id', 'qualification_id'], 'idx_uqt_user_qualification');
            $table->index('exam_date', 'idx_uqt_exam_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_qualification_targets');
    }
};
