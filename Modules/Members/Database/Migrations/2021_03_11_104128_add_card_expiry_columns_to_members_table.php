<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCardExpiryColumnsToMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('card_expires_month')->nullable()->after('payment_is_recurring');
            $table->string('card_expires_year')->nullable()->after('card_expires_month');
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['card_expires_month', 'card_expires_year']);
        });
    }
}
