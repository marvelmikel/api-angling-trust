<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationQueueTable extends Migration
{
    public function up()
    {
        Schema::create('notification_queue', function (Blueprint $table)
        {
            $table->bigIncrements('id');

            $table->boolean('customer_notification')->default(0);
            $table->boolean('admin_notification')->default(0);
            $table->string('reference');
            $table->string('to')->nullable();

            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_queue');
    }
}
