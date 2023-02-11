<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWaterTypeColumnToStationsTable extends Migration
{
    public function up()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->string('water_type')->after('name');
        });
    }

    public function down()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->dropColumn(['water_type']);
        });
    }
}
