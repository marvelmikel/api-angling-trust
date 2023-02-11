<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStationsTable extends Migration
{
    public function up()
    {
        Schema::create('stations', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->string('ref')->unique();

            // Columns
            $table->string('type');
            $table->string('name');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->text('stats')->nullable();

            // Config
            $table->dateTime('stats_updated_at')->nullable();
            $table->dateTime('readings_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stations');
    }
}
