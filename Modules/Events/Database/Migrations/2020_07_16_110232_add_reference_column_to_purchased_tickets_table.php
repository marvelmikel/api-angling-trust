<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferenceColumnToPurchasedTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('purchased_tickets', function (Blueprint $table) {
            $table->string('reference')->after('ticket_id');
        });
    }

    public function down()
    {
        //
    }
}
