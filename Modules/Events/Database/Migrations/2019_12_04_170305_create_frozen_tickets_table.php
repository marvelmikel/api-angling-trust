<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFrozenTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('frozen_tickets', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ticket_id');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');

            // Columns
            $table->string('token');

            // Config
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('frozen_tickets');
    }
}
