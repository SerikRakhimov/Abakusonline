<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('relit_id')->default(0);
            $table->unsignedBigInteger('child_project_id')->default(0);
            $table->unsignedbigInteger('parent_project_id')->default(0);
            $table->timestamps();
            $table->index('relit_id');
            $table->index('child_project_id');
            $table->index('parent_project_id');
            $table->unique(['child_project_id', 'relit_id']);
            $table->foreign('relit_id')->references('id')->on('relits')->onDelete('cascade');
            $table->foreign('child_project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('parent_project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relips');
    }
}
