<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('membership_type_id');
            $table->foreign('membership_type_id')->references('id')->on('membership_types');

            // Columns
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('disability')->nullable();
            $table->string('home_telephone')->nullable();
            $table->string('mobile_telephone')->nullable();

            // Config
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('frozen_on')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
}
