<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBasketIdColumnToTicketTables extends Migration
{
    public function up()
    {
        Schema::table('frozen_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('basket_id')->after('id')->nullable();
            $table->foreign('basket_id')->references('id')->on('ticket_baskets');
        });

        Schema::table('purchased_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('basket_id')->after('id')->nullable();
            $table->foreign('basket_id')->references('id')->on('ticket_baskets');
        });
    }

    public function down()
    {
        Schema::table('frozen_tickets', function (Blueprint $table) {
            $table->dropForeign('basket_id');
            $table->dropColumn(['basket_id']);
        });

        Schema::table('purchased_tickets', function (Blueprint $table) {
            $table->dropForeign('basket_id');
            $table->dropColumn(['basket_id']);
        });
    }
}
