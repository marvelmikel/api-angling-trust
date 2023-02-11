<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCanceledAtColumnToPurchasedTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('purchased_tickets', function (Blueprint $table) {
            $table->dateTime('canceled_at')->nullable()->after('purchased_at');
        });
    }

    public function down()
    {
        Schema::table('purchased_tickets', function (Blueprint $table) {
            $table->dropColumn(['canceled_at']);
        });
    }
}
