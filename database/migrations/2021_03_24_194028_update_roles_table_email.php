<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRolesTableEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_edit_email_base_create')->default(false);
            $table->boolean('is_edit_email_question_base_create')->default(false);
            $table->boolean('is_edit_email_base_update')->default(false);
            $table->boolean('is_edit_email_question_base_update')->default(false);
            $table->boolean('is_show_email_base_delete')->default(false);
            $table->boolean('is_show_email_question_base_delete')->default(false);
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
