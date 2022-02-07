<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Base;

class UpdateBasesTableDescAdd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bases', function (Blueprint $table) {
            $table->string('desc_lang_0', 255)->default("");
            $table->string('desc_lang_1', 255)->default("");
            $table->string('desc_lang_2', 255)->default("");
            $table->string('desc_lang_3', 255)->default("");
        });
        // Присвоить начальные значения полям 'desc_lang_*'
        $bases = Base::all();
        foreach ($bases as $base) {
            if ($base->desc_lang_0 == "") {
                $base->desc_lang_0 = $base->name_lang_0;
            }
            if ($base->desc_lang_1 == "") {
                $base->desc_lang_1 = $base->name_lang_1;
            }
            if ($base->desc_lang_2 == "") {
                $base->desc_lang_2 = $base->name_lang_2;
            }
            if ($base->desc_lang_3 == "") {
                $base->desc_lang_3 = $base->name_lang_3;
            }
            $base->save();
        }
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
