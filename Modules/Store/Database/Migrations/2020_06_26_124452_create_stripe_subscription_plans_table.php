<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeSubscriptionPlansTable extends Migration
{
    public function up()
    {
        Schema::create('stripe_subscription_plans', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->string('api_id');
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
        Schema::dropIfExists('stripe_subscription_plans');
    }
}
