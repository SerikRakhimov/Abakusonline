<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRobasTableSkipRecordsEqual1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robas', function (Blueprint $table) {
            $table->boolean('is_skip_count_records_equal_1_base_index')->default(false);
            $table->boolean('is_skip_count_records_equal_1_item_body_index')->default(false);
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
