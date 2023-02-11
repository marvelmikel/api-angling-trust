<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFishingDrawsTable extends Migration
{
    public function up()
    {
        Schema::create('fishing_draws', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedInteger('wp_id')->unique();

            $table->string('name');
            $table->dateTime('opens_at');
            $table->dateTime('closes_at');
            $table->integer('ticket_price');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fishing_draws');
    }
}
