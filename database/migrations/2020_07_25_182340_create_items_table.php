<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(768);
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('base_id')->default(0);
            // размер строкового поля 255 также прописан в ItemController::calc_value_func()
            $table->string('code',50)->default("");
            $table->string('name_lang_0',255)->default("");
            $table->string('name_lang_1',255)->default("");
            $table->string('name_lang_2',255)->default("");
            $table->string('name_lang_3',255)->default("");
            $table->timestamps();
            $table->index('base_id');
            //$table->unique(['base_id', 'code']);
            //$table->index('name_lang_0');
            //$table->index('name_lang_1');
            //$table->index('name_lang_2');
            //$table->index('name_lang_3');
//          $table->unique(['base_id', 'name_lang_0']);
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
        Schema::dropIfExists('items');
    }
}
