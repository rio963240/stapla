<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('study_plan_items', function (Blueprint $table) {
            //主キー(自動採番)
            $table->bigIncrements('study_plan_items_id');

            //todoリストID(FK->todo.todo_id)
            $table->unsignedBigInteger('todo_id');
            $table->foreign('todo_id','fk_sqi_todo')
            ->references('todo_id')
            ->on('todo')
            ->cascadeOnDelete();

            /**
             * 分野ID（FK → qualification_domains.qualification_domains_id）
             * サブ分野ID（FK → qualification_subdomains.qualification_subdomains_id）
             *
             * どちらを使うかは運用次第なので NULL 許容にして、
             * 「少なくともどちらか一方は必須」をDBで担保します（下でCHECK制約）
             */
            $table->unsignedBigInteger('qualification_domains_id')->nullable();
            $table->foreign('qualification_domains_id', 'fk_spi_qd')
                ->references('qualification_domains_id')
                ->on('qualification_domains')
                ->onDelete('restrict');

            $table->unsignedBigInteger('qualification_subdomains_id')->nullable();
            $table->foreign('qualification_subdomains_id', 'fk_spi_qsd')
                ->references('qualification_subdomains_id')
                ->on('qualification_subdomains')
                ->onDelete('restrict');

            //予定勉強時間
            $table->unsignedSmallInteger('planned_minutes');

            //完了/未完了
            $table->boolean('status')->default(false);

            //検索用インデックス
            $table->index('todo_id', 'idx_spi_todo');
            $table->index('qualification_domains_id', 'idx_spi_domain');
            $table->index('qualification_subdomains_id', 'idx_spi_subdomain');

        });
        //CHECK制約：domain/subdomainのどちらか一方は必須
        DB::statement("
            ALTER TABLE study_plan_items ADD CONSTRAINT ck_spi_domain_or_subdomain CHECK (
                qualification_domains_id IS NOT NULL OR qualification_subdomains_id IS NOT NULL
            )
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //CHECK制約を先に落とす(存在しなくても落ちないようにIF EXISTS)
        DB::stattment("ALTER TABLE study_plan_items DROP CONSTRAINT IF EXISTS ck_spi_domain_or subdomain");
        Schema::dropIfExists('study_plan_items');
    }
};
