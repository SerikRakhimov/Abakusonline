<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSetsTableFunc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->boolean('is_upd_cl_fn_avg')->default(false);
            $table->boolean('is_upd_cl_fn_count')->default(false);
            $table->boolean('is_upd_cl_fn_sum')->default(false);
            // Удалить эти поля
            // $set->is_upd_plus;
            // $set->is_upd_minus;
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
