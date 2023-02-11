<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropNameColumnsFromUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'first_name',
                'last_name',
                'full_name'
            ]);
        });
    }

    public function down()
    {
        //
    }
}
