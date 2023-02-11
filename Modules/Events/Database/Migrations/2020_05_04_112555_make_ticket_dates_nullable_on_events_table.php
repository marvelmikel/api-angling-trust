<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeTicketDatesNullableOnEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('ticket_sales_open')->nullable()->change();
            $table->dateTime('ticket_sales_close')->nullable()->change();
        });
    }

    public function down()
    {
        //
    }
}
