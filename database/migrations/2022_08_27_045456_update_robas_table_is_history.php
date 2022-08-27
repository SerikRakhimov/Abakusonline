<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRobasTableIsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robas', function (Blueprint $table) {
            $table->boolean('is_show_hist_attr_enable')->default(false);
            $table->boolean('is_edit_hist_attr_enable')->default(false);
            $table->boolean('is_list_hist_attr_enable')->default(false);
            $table->boolean('is_list_hist_records_enable')->default(true);
            $table->boolean('is_brow_hist_attr_enable')->default(false);
            $table->boolean('is_brow_hist_records_enable')->default(true);
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
