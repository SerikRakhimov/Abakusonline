<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTemplatesTableIsShowAdminIsCreateAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('templates', function (Blueprint $table) {
            // Показывать шаблон (в списке шаблонов) только админу
            $table->boolean('is_show_admin')->default(false);
            // Создавать проект из этого шаблона (в списке шаблонов) может только админ
            $table->boolean('is_create_admin')->default(false);
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
