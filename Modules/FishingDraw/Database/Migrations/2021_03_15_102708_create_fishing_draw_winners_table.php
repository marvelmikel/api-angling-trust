<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFishingDrawWinnersTable extends Migration
{
    public function up()
    {
        Schema::create('fishing_draw_winners', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('draw_id');
            $table->foreign('draw_id')->references('id')->on('fishing_draws');
            $table->unsignedBigInteger('prize_id');
            $table->foreign('prize_id')->references('id')->on('fishing_draw_prizes');
            $table->unsignedBigInteger('entry_id');
            $table->foreign('entry_id')->references('id')->on('fishing_draw_entries');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fishing_draw_winners');
    }
}
