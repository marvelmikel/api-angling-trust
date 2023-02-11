<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersIndexTable extends Migration
{
    public function up()
    {
        Schema::create('members_index', function (Blueprint $table)
        {
            // Keys
            $table->bigIncrements('id');
            $table->unsignedBigInteger('member_id')->unique();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->boolean('at_member');
            $table->boolean('fl_member');

            // Columns
            $table->string('reference');
            $table->unsignedBigInteger('membership_type_id');
            $table->string('membership_type_slug');
            $table->string('membership_type_name');
            $table->string('full_name')->nullable();
            $table->string('email');
            $table->string('address_postcode')->nullable();
            $table->longText('primary_contact')->nullable();
            $table->boolean('is_suspended')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('registered_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('members_index');
    }
}
