<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFisheriesTable extends Migration
{
    public function up()
    {
        Schema::create('fisheries', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Columns
            // todo: tbd

            // Config
            $table->boolean('auto_renew');
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('frozen_on')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fisheries');
    }
}
