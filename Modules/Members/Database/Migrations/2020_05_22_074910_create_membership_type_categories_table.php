<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembershipTypeCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('membership_type_categories', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedBigInteger('membership_type_id');

            $table->string('name');
            $table->string('slug');
            $table->integer('price');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_type_categories');
    }
}
