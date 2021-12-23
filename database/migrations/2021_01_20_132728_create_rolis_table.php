<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rolis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->default(0);
            $table->unsignedBigInteger('link_id')->default(0);
            $table->boolean('is_list_link_enable')->default(true);
            $table->boolean('is_show_link_enable')->default(true);
            $table->boolean('is_edit_link_read')->default(false);
            $table->boolean('is_edit_link_update')->default(true);
            $table->timestamps();
            $table->index('role_id');
            $table->index('link_id');
            $table->unique(['role_id', 'link_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rolis');
    }
}
