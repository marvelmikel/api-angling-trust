<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotingFormQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voting_form_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('voting_form_id');
            $table->string('type');
            $table->text('content')->nullable();
            $table->integer('min')->default(0);
            $table->integer('max')->default(0);
            $table->integer('order');
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
        Schema::dropIfExists('voting_form_questions');
    }
}
