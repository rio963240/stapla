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
        Schema::create('qualification_subdomains', function (Blueprint $table) {
            //主キー（自動採番）
            $table->bigIncrements('qualification_subdomains_id');

            //分野ID(FK->qualification_domains.qualification_domains_id)
            $table->unsignedBigInteger('qualification_domains_id');
            $table->foreign('qualification_domains_id')->references('qualification_domains_id')->on('qualification_domains')->cascadeOnDelete();

            //サブ分野名称
            $table->string('name',255);

            //有効/無効（デフォルト true）
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            //複合UNIQUE(qualification_domains_id,name)
            $table->unique(['qualification_domains_id','name'],'uq_qualification_subdomains_domain_id_name');

            //index(qualification_domains_id)
            $table->index('qualification_domains_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualification_subdomains');
    }
};
