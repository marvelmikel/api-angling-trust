<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberSelectOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('member_select_options', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('type');
            $table->string('name');
            $table->string('slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_select_options');
    }
}
