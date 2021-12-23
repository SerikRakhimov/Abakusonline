<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id')->default(0);
            $table->string('name_lang_0',255)->default("");
            $table->string('name_lang_1',255)->default("");
            $table->string('name_lang_2',255)->default("");
            $table->string('name_lang_3',255)->default("");
            $table->boolean('is_default_for_external')->default(false);
            $table->timestamps();
            $table->index('template_id');
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
