<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSmartDebitIdColumnToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('smart_debit_id')->nullable()->after('trial_ends_at');
            $table->string('smart_debit_frequency')->nullable()->after('smart_debit_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['smart_debit_id', 'smart_debit_frequency']);
        });
    }
}
