<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBillingFrequencyColumnFromMembershipTypesTable extends Migration
{
    public function up()
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropColumn(['billing_frequency']);
        });
    }

    public function down()
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->string('billing_frequency')->after('slug');
        });
    }
}
