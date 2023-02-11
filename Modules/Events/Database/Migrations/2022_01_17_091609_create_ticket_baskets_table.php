<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketBasketsTable extends Migration
{
    public function up()
    {
        Schema::create('ticket_baskets', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedBigInteger('member_id')->nullable();

            $table->string('reference');
            $table->string('payment_id')->nullable();
            $table->integer('price')->default(0);

            // Timestamps
            $table->dateTime('expires_at');
            $table->dateTime('purchased_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_baskets');
    }
}
