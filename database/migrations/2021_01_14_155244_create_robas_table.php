<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRobasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->default(0);
            $table->unsignedBigInteger('base_id')->default(0);
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
            $table->timestamps();
            $table->index('role_id');
            $table->index('base_id');
            $table->unique(['role_id', 'base_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('base_id')->references('id')->on('bases')->onDelete('cascade');
        });




    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('robas');
    }
}
