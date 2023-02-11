<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFishingLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('fishing_locations', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedInteger('wp_id')->unique();

            // Columns
            $table->string('name');
            $table->string('discipline_id')->nullable();
            $table->string('water_type')->nullable();
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->text('coaches')->nullable();
            $table->text('events')->nullable();
            $table->unsignedBigInteger('station_id')->nullable();

            // Config
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fishing_locations');
    }
}
