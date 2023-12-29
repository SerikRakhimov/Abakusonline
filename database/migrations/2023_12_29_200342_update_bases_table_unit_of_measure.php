<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBasesTableUnitOfMeasure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bases', function (Blueprint $table) {
            $table->string('unit_meas_desc_0', 255)->default("");
            $table->string('unit_meas_desc_1', 255)->default("");
            $table->string('unit_meas_desc_2', 255)->default("");
            $table->string('unit_meas_desc_3', 255)->default("");
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
