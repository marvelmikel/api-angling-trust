<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceColumnToPurchasedTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchased_tickets', function (Blueprint $table) {
            $table->renameColumn('your_details', 'data');
            $table->integer('price')->after('payment_id');
        });
    }

    public function down()
    {
        //
    }
}
