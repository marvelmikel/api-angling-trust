<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnboardRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onboard_records', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('member_id');

            $table->integer('emails_sent')->default(0);
            $table->dateTime('email_last_sent')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('onboards');
    }
}
