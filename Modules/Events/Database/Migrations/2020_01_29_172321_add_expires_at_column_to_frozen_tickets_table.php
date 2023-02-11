<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpiresAtColumnToFrozenTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('frozen_tickets', function (Blueprint $table) {
            $table->dateTime('expires_at')->after('token');
        });
    }

    public function down()
    {
        Schema::table('frozen_tickets', function (Blueprint $table) {
            $table->dropColumn(['expires_at']);
        });
    }
}
