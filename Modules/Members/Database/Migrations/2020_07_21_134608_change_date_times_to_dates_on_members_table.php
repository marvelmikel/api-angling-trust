<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateTimesToDatesOnMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->date('registered_at')->change();
            $table->date('renewed_at')->change();
            $table->date('expires_at')->change();
        });
    }

    public function down()
    {
        //
    }
}
