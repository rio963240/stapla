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
        Schema::table('user_qualification_targets', function (Blueprint $table) {
            $table->unique(
                ['user_id','qualification_id'],
                'uq_uqt_user_qualification'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_qualification_targets', function (Blueprint $table) {
            $table->dropUnique('uq_uqt_user_qualification');
        });
    }
};
