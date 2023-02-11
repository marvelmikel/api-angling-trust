<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->morphs('addressable');

            // Columns
            $table->string('line_1')->nullable();
            $table->string('line_2')->nullable();
            $table->string('town')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
