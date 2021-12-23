<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_id')->default(0);
            $table->unsignedBigInteger('child_item_id')->default(0);
            $table->unsignedbigInteger('parent_item_id')->default(0);
            $table->timestamps();
            $table->index('link_id');
            $table->index('child_item_id');
            $table->index('parent_item_id');
            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
            $table->foreign('child_item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('parent_item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mains');
    }
}
