<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Role;

class UpdateRolesTableIsExternal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_external')->default(false);
        });
        // Обновить поле
        $roles = Role::all();
        foreach ($roles as $role) {
            if ($role->is_default_for_external == true) {
                $role->is_external = 1;
                $role->save();
            }
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
