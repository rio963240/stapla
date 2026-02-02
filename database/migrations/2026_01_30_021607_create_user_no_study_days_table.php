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
        Schema::create('user_no_study_days', function (Blueprint $table) {
            //主キー
            $table->bigIncrements('user_no_study_days_id');

            //資格目標ID(FK->user_qualification_taegets.user_qualification_taegets_id)
            $table->unsignedBigInteger('user_qualification_targets_id');
            $table->foreign('user_qualification_targets_id', 'fk_unstd_uqt')
                ->references('user_qualification_targets_id')
                ->on('user_qualification_targets')
                ->cascadeOnDelete();

            //勉強不可日
            $table->date('no_study_day');

            //同一目標で同じ日を二重登録させない
            $table->unique(
                ['user_qualification_targets_id', 'no_study_day'],
                'uq_unstd_taeget_day'
            );

            //検索用
            $table->index('user_qualification_targets_id', 'idx_unstd_target');
            $table->index('no_study_day', 'idx_unstd_day');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_no_study_days');
    }
};
