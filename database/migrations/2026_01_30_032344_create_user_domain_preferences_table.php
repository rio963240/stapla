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
        Schema::create('user_domain_preferences', function (Blueprint $table) {
            //主キー(自動採番)
            $table->bigIncrements('user_domain_preferences_id');

            //資格目標ID(FK->user_qualification_targets.user_qualification_targets_id)
            $table->unsignedBigInteger('user_qualification_targets_id');
            $table->foreign('user_qualification_targets_id','fk_udp_uqt')
            ->references('user_qualification_targets_id')
            ->on('user_qualification_targets')
            ->cascadeOnDelete();

            //分野ID(FK->qualification_domains.qualification_domains_id)
            $table->unsignedBigInteger('qualification_domains_id');
            $table->foreign('qualification_domains_id','fk_udp_qd')
            ->references('qualification_domains_id')
            ->on('qualification_domains')
            ->onDelete('restrict'); //マスタ誤削除を防ぐ

            //重み
            $table->unsignedSmallInteger('weight');

            //同一目標*同一分野を二重登録させない(複合ユニーク)
            $table->unique(
                ['user_qualification_targets_id','qualification_domains_id'],
                'uq_udp_target_domain'
            );

            //検索用インデックス
            $table->index('user_qualification_targets_id','idx_udp_target');
            $table->index('qualification_domains_id','idx_udp_domain');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_domain_preferences');
    }
};
