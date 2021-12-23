<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id')->default(0);
            $table->integer('serial_number')->default(0);
            $table->unsignedBigInteger('link_from_id')->default(0);
            $table->unsignedBigInteger('link_to_id')->default(0);
            $table->boolean('is_group')->default(false);
            $table->boolean('is_update')->default(false);
            //$table->boolean('is_upd_plus')->default(false);
            //$table->boolean('is_upd_minus')->default(false);
            $table->boolean('is_upd_replace')->default(false);
            $table->timestamps();
            $table->index('template_id');
            $table->index('serial_number');
            $table->index('link_from_id');
            $table->index('link_to_id');
            //$table->unique(['serial_number', 'link_from_id', 'link_to_id']);
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
            $table->foreign('link_from_id')->references('id')->on('links')->onDelete('cascade');
            $table->foreign('link_to_id')->references('id')->on('links')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sets');
    }
}
