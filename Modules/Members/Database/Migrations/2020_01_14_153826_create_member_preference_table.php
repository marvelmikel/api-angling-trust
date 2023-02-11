<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberPreferenceTable extends Migration
{
    public function up()
    {
        Schema::create('member_preference', function (Blueprint $table)
        {
            // Keys
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->unsignedBigInteger('preference_id');
            $table->foreign('preference_id')->references('id')->on('preferences')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_preference');
    }
}
