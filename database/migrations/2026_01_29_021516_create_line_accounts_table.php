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
        Schema::create('line_accounts', function (Blueprint $table) {
            //主キー
            $table->bigIncrements('line_accounts_id');

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('line_user_id',255)->unique();
            $table->string('line_link_token',255)->nullable();
            $table->string('is_linked')->default(false);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_accounts');
    }
};
