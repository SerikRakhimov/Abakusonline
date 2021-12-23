<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_author')->default(false);
            $table->boolean('is_list_base_sndbt')->default(false);
            $table->boolean('is_list_base_id')->default(false);
            $table->boolean('is_list_base_create')->default(true);
            $table->boolean('is_list_base_read')->default(false);
            $table->boolean('is_list_base_update')->default(true);
            $table->boolean('is_list_base_delete')->default(true);
            $table->boolean('is_list_base_used_delete')->default(false);
            $table->boolean('is_list_base_byuser')->default(false);
            $table->boolean('is_edit_base_read')->default(false);
            $table->boolean('is_edit_base_update')->default(true);
            $table->boolean('is_list_base_enable')->default(true);
            $table->boolean('is_list_link_enable')->default(true);
            $table->boolean('is_show_base_enable')->default(true);
            $table->boolean('is_show_link_enable')->default(true);
            $table->boolean('is_edit_link_read')->default(false);
            $table->boolean('is_edit_link_update')->default(true);
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
