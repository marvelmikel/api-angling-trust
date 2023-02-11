<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RebuildMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {

            $table->renameColumn('ref', 'reference');

            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('home_telephone')->nullable();
            $table->string('mobile_telephone')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_town')->nullable();
            $table->string('address_county')->nullable();
            $table->string('address_postcode')->nullable();
            $table->string('payment_provider')->nullable();
            $table->boolean('payment_is_recurring')->nullable();

            $table->dateTime('membership_pack_sent_at')->nullable();
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('renewed_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        //
    }
}
