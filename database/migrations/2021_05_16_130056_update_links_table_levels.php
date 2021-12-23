<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLinksTableLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_level_id_0')->nullable()->default(null);
            $table->unsignedBigInteger('parent_level_id_1')->nullable()->default(null);
            $table->unsignedBigInteger('parent_level_id_2')->nullable()->default(null);
            $table->unsignedBigInteger('parent_level_id_3')->nullable()->default(null);
            $table->index('parent_level_id_0');
            $table->index('parent_level_id_1');
            $table->index('parent_level_id_2');
            $table->index('parent_level_id_3');
            $table->foreign('parent_level_id_0')->references('id')->on('levels')->onDelete('set null');
            $table->foreign('parent_level_id_1')->references('id')->on('levels')->onDelete('set null');
            $table->foreign('parent_level_id_2')->references('id')->on('levels')->onDelete('set null');
            $table->foreign('parent_level_id_3')->references('id')->on('levels')->onDelete('set null');
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
