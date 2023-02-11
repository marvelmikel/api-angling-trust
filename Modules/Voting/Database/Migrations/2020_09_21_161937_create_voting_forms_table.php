<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotingFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voting_forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('wp_id');
            $table->string('title');
            $table->longText('text')->nullable();
            $table->longText('confirmation_text')->nullable();
            $table->longText('intro_text')->nullable();
            $table->text('intro_confirmation_text')->nullable();
            $table->string('membership_type');
            $table->tinyInteger('at');
            $table->tinyInteger('fl');
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
        Schema::dropIfExists('voting_forms');
    }
}
