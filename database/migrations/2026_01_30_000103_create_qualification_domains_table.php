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
        Schema::create('qualification_domains', function (Blueprint $table) {
            //主キー（自動採番）
            $table->bigIncrements('qualification_domains_id');
            //資格ID(FK->qualification.qualification_id)
            $table->unsignedBigInteger('qualification_id');
            $table->foreign('qualification_id')->references('qualification_id')->on('qualification')->restrictOnDelete();

            //分野名称
            $table->string('name',255);

            //有効/無効（デフォルト true）
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            //複合UNIQUE(qualification_id,name)
            $table->unique(['qualification_id','name'],'uq_qualification_domains_qualification_id_name');

            //INDEX(qualification_id)
            $table->index('qualification_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualification_domains');
    }
};
