<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartEndDatesToEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('start_date')->after('pools_payments');
            $table->dateTime('end_date')->after('start_date');
        });
    }

    public function down()
    {
        //
    }
}
