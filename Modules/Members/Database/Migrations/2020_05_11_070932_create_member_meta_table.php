<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberMetaTable extends Migration
{

    public function up()
    {
        Schema::create('member_meta', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedBigInteger('member_id');

            // Columns
            $table->string('key');
            $table->string('cast')->nullable();
            $table->longText('value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('members_meta');
    }
}
