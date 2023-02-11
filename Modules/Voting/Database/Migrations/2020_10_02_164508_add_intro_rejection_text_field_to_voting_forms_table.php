<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIntroRejectionTextFieldToVotingFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voting_forms', function (Blueprint $table) {
            $table->string('intro_rejection_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voting_forms', function (Blueprint $table) {
            $table->dropColumn('intro_rejection_text');
        });
    }
}
