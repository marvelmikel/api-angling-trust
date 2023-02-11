<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRejectionTextToVotingFormsTable extends Migration
{
    public function up()
    {
        Schema::table('voting_forms', function (Blueprint $table) {
            $table->longText('rejection_text')->nullable();
        });
    }

    public function down()
    {
        Schema::table('voting_forms', function (Blueprint $table) {
            $table->dropColumn(['rejection_text']);
        });
    }
}
