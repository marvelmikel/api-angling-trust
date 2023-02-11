<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedBigInteger('member_id')->nullable();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->string('reference');

            // Columns
            $table->string('payment_provider');
            $table->integer('amount');
            $table->boolean('auto_renew');

            // Config
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
