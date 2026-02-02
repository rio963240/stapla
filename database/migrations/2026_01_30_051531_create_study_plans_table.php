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
        Schema::create('study_plans', function (Blueprint $table) {
            //主キー(自動採番)
            $table->bigIncrements('study_plans_id');

            //資格目標ID(FK->user_qualification_targets.user_qualification_targets_id)
            $table->unsignedBigInteger('user_qualification_targets_id');
            $table->foreign('user_qualification_targets_id','fk_sp_uqt')
            ->references('user_qualification_targets_id')
            ->on('user_qualification_targets')
            ->cascadeOnDelete();

            //バージョン(同一目標ないで1,2,3...)
            $table->unsignedSmallInteger('version')->default(1);

            //有効/無効
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            //versionの一意性は目標ごとに担保
            $table->unique(
                ['user_qualification_targets_id','version'],
                'uq_sp_target_version'
            );

            //検索用インデックス
            $table->index('user_qualification_targets_id','idx_sp_target');
            $table->index(['user_qualification_targets_id','is_active'],'idx_sp_target_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plans');
    }
};
