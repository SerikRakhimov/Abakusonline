<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRolesTableAddDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
//          $table->text('desc_lang_0')->nullable();
            $table->text('desc_lang_0')->nullable();
            $table->text('desc_lang_1')->nullable();
            $table->text('desc_lang_2')->nullable();
            $table->text('desc_lang_3')->nullable();
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
