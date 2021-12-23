<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id')->default(0);
            $table->unsignedBigInteger('user_id')->default(0);
            $table->string('name_lang_0',255)->default("");
            $table->string('name_lang_1',255)->default("");
            $table->string('name_lang_2',255)->default("");
            $table->string('name_lang_3',255)->default("");
            $table->timestamps();
            $table->index('template_id');
            $table->index('user_id');
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
