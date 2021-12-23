<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAccessesTableRequestPermit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accesses', function (Blueprint $table) {
            // Запрос на подписку
            $table->boolean('is_subscription_request')->default(false);
//            // Подписка разрешена
//            $table->boolean('is_subscription_allowed')->default(false);
            // Доступ разрешен
            $table->boolean('is_access_allowed')->default(false);
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
