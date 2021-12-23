<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bases', function (Blueprint $table) {
            $table->id();
            $table->string('name_lang_0',255)->default("");
            $table->string('name_lang_1',255)->default("");
            $table->string('name_lang_2',255)->default("");
            $table->string('name_lang_3',255)->default("");
            $table->string('names_lang_0',255)->default("");
            $table->string('names_lang_1',255)->default("");
            $table->string('names_lang_2',255)->default("");
            $table->string('names_lang_3',255)->default("");
            $table->boolean('type_is_list')->default(false);
            $table->boolean('type_is_number')->default(false);
            $table->boolean('type_is_string')->default(false);
            $table->boolean('type_is_date')->default(false);
            $table->boolean('type_is_boolean')->default(false);
            $table->boolean('is_code_needed')->default(false);
            $table->boolean('is_code_number')->default(false);
            $table->boolean('is_limit_sign_code')->default(false);
            $table->integer('significance_code')->default(0);
            $table->boolean('is_code_zeros')->default(false);
            $table->boolean('is_suggest_code')->default(false);
            $table->boolean('is_suggest_max_code')->default(false);
            $table->boolean('is_recalc_code')->default(false);
            $table->integer('digits_num')->default(0);
            $table->boolean('is_required_lst_num_str_txt_img_doc')->default(false);
            $table->boolean('is_one_value_lst_str_txt')->default(false);
            $table->boolean('is_calcname_lst')->default(false);
            $table->string('sepa_calcname',255)->default(",");
            $table->boolean('is_same_small_calcname')->default(false);
            $table->string('sepa_same_left_calcname',255)->default("");
            $table->string('sepa_same_right_calcname',255)->default("");
            $table->index('name_lang_0');
            $table->index('name_lang_1');
            $table->index('name_lang_2');
            $table->index('name_lang_3');
            $table->index('names_lang_0');
            $table->index('names_lang_1');
            $table->index('names_lang_2');
            $table->index('names_lang_3');
            $table->timestamps();
//          $table->unique("name_lang_0");
//          $table->unique("names_lang_0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bases');
    }
}
