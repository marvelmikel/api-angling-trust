<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('options', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');

            // Columns
            $table->string('key')->unique();
            $table->string('type');
            $table->longText('value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('options');
    }
}
