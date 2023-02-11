<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembershipTypesTable extends Migration
{
    public function up()
    {
        Schema::create('membership_types', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedInteger('wp_id')->unique();

            // Columns
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('billing_frequency');
            $table->integer('price');

            // Config
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_types');
    }
}
