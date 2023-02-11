<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdatedAtColumnToMembersIndexTable extends Migration
{
    public function up()
    {
        Schema::table('members_index', function (Blueprint $table) {
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('members_index', function (Blueprint $table) {
            $table->dropColumn(['updated_at']);
        });
    }
}
