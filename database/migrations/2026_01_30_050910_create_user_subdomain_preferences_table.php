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
        Schema::create('user_subdomain_preferences', function (Blueprint $table) {

             //主キー（自動採番）
            $table->bigIncrements('user_subdomain_preferences_id');


            //資格目標ID（FK → user_qualification_targets.user_qualification_targets_id）
            $table->unsignedBigInteger('user_qualification_targets_id');
            $table->foreign('user_qualification_targets_id', 'fk_usdp_uqt')
                ->references('user_qualification_targets_id')
                ->on('user_qualification_targets')
                ->cascadeOnDelete();


            //サブ分野ID（FK → qualification_subdomains.qualification_subdomains_id）
            $table->unsignedBigInteger('qualification_subdomains_id');
            $table->foreign('qualification_subdomains_id', 'fk_usdp_qsd')
                ->references('qualification_subdomains_id')
                ->on('qualification_subdomains')
                ->onDelete('restrict'); // マスタ誤削除防止（推奨）


            //重み（0〜99 想定）
            $table->unsignedSmallInteger('weight');

            //同一目標 × 同一サブ分野 を二重登録させない
            $table->unique(
                ['user_qualification_targets_id', 'qualification_subdomains_id'],
                'uq_usdp_target_subdomain'
            );

            //検索用インデックス
            $table->index('user_qualification_targets_id', 'idx_usdp_target');
            $table->index('qualification_subdomains_id', 'idx_usdp_subdomain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subdomain_preferences');
    }
};
