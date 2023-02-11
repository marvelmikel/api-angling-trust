<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedInteger('wp_id')->unique();

            // Columns
            $table->string('name');
            $table->string('slug');
            $table->dateTime('ticket_sales_open');
            $table->dateTime('ticket_sales_close');

            // Config
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}
