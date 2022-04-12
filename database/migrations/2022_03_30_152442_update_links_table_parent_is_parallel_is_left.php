<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLinksTableParentIsParallelIsLeft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->boolean('parent_is_parallel')->default(false);
            $table->boolean('parent_is_left_calcname_lang_0')->default(false);
            $table->boolean('parent_is_left_calcname_lang_1')->default(false);
            $table->boolean('parent_is_left_calcname_lang_2')->default(false);
            $table->boolean('parent_is_left_calcname_lang_3')->default(false);
            //$table->dropColumn('parent_is_left_calcname');
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
