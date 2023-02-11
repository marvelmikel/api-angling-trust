<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAtFlColumnsToMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->boolean('at_member')->after('reference')->default(0);
            $table->boolean('fl_member')->after('at_member')->default(0);
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'at_member',
                'fl_member'
            ]);
        });
    }
}
