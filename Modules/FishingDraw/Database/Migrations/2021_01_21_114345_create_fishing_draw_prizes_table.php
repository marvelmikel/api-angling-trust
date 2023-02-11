<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFishingDrawPrizesTable extends Migration
{
    public function up()
    {
        Schema::create('fishing_draw_prizes', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedInteger('wp_id')->unique();
            $table->unsignedBigInteger('draw_id');
            $table->foreign('draw_id')->references('id')->on('fishing_draws');

            $table->string('name');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fishing_draw_prizes');
    }
}
