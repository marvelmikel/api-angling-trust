<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptInColumnsToMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->boolean('opt_in_1')->default(0)->after('is_imported');
            $table->boolean('opt_in_2')->default(0)->after('opt_in_1');
        });
    }

    public function down()
    {
        //
    }
}
