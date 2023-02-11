<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFishingDrawEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fishing_draw_entries', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('draw_id');
            $table->foreign('draw_id')->references('id')->on('fishing_draws');
            $table->unsignedBigInteger('prize_id');
            $table->foreign('prize_id')->references('id')->on('fishing_draw_prizes');
            $table->unsignedBigInteger('member_id')->nullable();

            $table->string('reference');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->integer('quantity');
            $table->integer('payment_amount');
            $table->string('payment_id')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fishing_draw_entries');
    }
}
