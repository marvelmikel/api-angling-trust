<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraDetailsToMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('ethnicity')->nullable();
            $table->string('sport_england_region')->nullable();
            $table->string('marine_region')->nullable();
        });
    }


    public function down()
    {
        Schema::table('members', function (Blueprint $table) {

        });
    }
}
