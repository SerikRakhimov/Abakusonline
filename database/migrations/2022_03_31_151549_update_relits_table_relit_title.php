<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRelitsTableRelitTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relits', function (Blueprint $table) {
            $table->string('parent_title_lang_0', 255)->default("");
            $table->string('parent_title_lang_1', 255)->default("");
            $table->string('parent_title_lang_2', 255)->default("");
            $table->string('parent_title_lang_3', 255)->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
