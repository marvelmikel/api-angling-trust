<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisabilityColumnsToMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['disability']);

            $table->string('disability_1')->after('postcode')->nullable();
            $table->string('disability_2')->after('disability_1')->nullable();
            $table->string('disability_3')->after('disability_2')->nullable();
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('disability');

            $table->dropColumn([
                'disability_1',
                'disability_2',
                'disability_3'
            ]);
        });
    }
}
