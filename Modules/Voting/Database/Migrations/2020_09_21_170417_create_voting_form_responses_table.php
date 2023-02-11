<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotingFormResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voting_form_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('voting_form_id');
            $table->bigInteger('voting_form_question_id');
            $table->bigInteger('member_id');
            $table->text('question');
            $table->integer('question_order');
            $table->text('response');
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
        Schema::dropIfExists('voting_form_responses');
    }
}
