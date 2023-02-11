<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStationReadingsTable extends Migration
{
    public function up()
    {
        Schema::create('station_readings', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedBigInteger('station_id');

            // Columns
            $table->decimal('value', 8, 3);

            // Config
            $table->dateTime('recorded_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('station_readings');
    }
}
