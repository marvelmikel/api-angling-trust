<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreferencesTable extends Migration
{
    public function up()
    {
        Schema::create('preferences', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');

            // Columns
            $table->string('type');
            $table->string('name');

            // Config
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('preferences');
    }
}
