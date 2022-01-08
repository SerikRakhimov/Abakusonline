<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relits', function (Blueprint $table) {
            $table->id();
            $table->integer('serial_number')->default(0);
            $table->unsignedBigInteger('child_template_id')->default(0);
            $table->unsignedbigInteger('parent_template_id')->default(0);
            $table->timestamps();
            $table->index('child_template_id');
            $table->index('parent_template_id');
            // Не нужно
            //$table->unique(['child_template_id', 'parent_template_id']);
            $table->foreign('child_template_id')->references('id')->on('templates')->onDelete('cascade');
            $table->foreign('parent_template_id')->references('id')->on('templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relits');
    }
}
