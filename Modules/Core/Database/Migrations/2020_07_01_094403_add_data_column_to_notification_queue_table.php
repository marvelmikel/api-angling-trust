<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataColumnToNotificationQueueTable extends Migration
{
    public function up()
    {
        Schema::table('notification_queue', function (Blueprint $table) {
            $table->longText('data')->nullable()->after('to');
        });
    }

    public function down()
    {
        //
    }
}
