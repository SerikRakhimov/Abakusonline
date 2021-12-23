<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_base_id')->default(null);
            $table->unsignedbigInteger('parent_base_id')->default(null);
            $table->string('child_label_lang_0',255)->default("");
            $table->string('child_label_lang_1',255)->default("");
            $table->string('child_label_lang_2',255)->default("");
            $table->string('child_label_lang_3',255)->default("");
            $table->string('child_labels_lang_0',255)->default("");
            $table->string('child_labels_lang_1',255)->default("");
            $table->string('child_labels_lang_2',255)->default("");
            $table->string('child_labels_lang_3',255)->default("");
            $table->integer('parent_base_number')->default(0);
            $table->string('parent_label_lang_0',255)->default("");
            $table->string('parent_label_lang_1',255)->default("");
            $table->string('parent_label_lang_2',255)->default("");
            $table->string('parent_label_lang_3',255)->default("");
            $table->boolean('parent_is_enter_refer')->default(false);
            $table->boolean('parent_is_calcname')->default(false);
            $table->boolean('parent_is_left_calcname')->default(false);
            $table->boolean('parent_is_small_calcname')->default(false);
            $table->string('parent_calcname_prefix_lang_0',255)->default("");
            $table->string('parent_calcname_prefix_lang_1',255)->default("");
            $table->string('parent_calcname_prefix_lang_2',255)->default("");
            $table->string('parent_calcname_prefix_lang_3',255)->default("");
            $table->boolean('parent_is_numcalc')->default(false);
            $table->boolean('parent_is_nc_screencalc')->default(false);
            $table->boolean('parent_is_nc_viewonly')->default(false);
            $table->boolean('parent_is_nc_parameter')->default(false);
            $table->boolean('parent_is_parent_related')->default(false);
            $table->boolean('parent_is_child_related')->default(false);
            $table->unsignedBigInteger('parent_parent_related_start_link_id')->default(0);
            $table->unsignedBigInteger('parent_parent_related_result_link_id')->default(0);
            $table->unsignedBigInteger('parent_child_related_start_link_id')->default(0);
            $table->unsignedBigInteger('parent_child_related_result_link_id')->default(0);
            $table->timestamps();
            $table->index('child_base_id');
            $table->index('parent_base_id');
//          $table->unique(['child_base_id', 'parent_base_id', 'child_label_lang_0', 'parent_label_lang_0']);
            $table->foreign('child_base_id')->references('id')->on('bases')->onDelete('cascade');
            $table->foreign('parent_base_id')->references('id')->on('bases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('links');
    }
}
