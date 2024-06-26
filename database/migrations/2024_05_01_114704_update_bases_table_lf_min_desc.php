<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBasesTableLfMinDesc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bases', function (Blueprint $table) {
            $table->string('lt_min_desc_0', 255)->default("");
            $table->string('lt_min_desc_1', 255)->default("");
            $table->string('lt_min_desc_2', 255)->default("");
            $table->string('lt_min_desc_3', 255)->default("");
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
