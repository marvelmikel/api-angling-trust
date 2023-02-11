<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropOldColumnsFromMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {

            $table->dropColumn([
                'date_of_birth',
                'gender',
                'postcode',
                'disability_1',
                'disability_2',
                'disability_3',
                'registration_source',
                'reason_for_joining',
                'auto_renew',
                'promo_code',
                'notes',
                'registered_at',
                'ethnicity',
                'sport_england_region',
                'marine_region',
                'payment_provider',
                'frozen_on',
                'created_at',
                'updated_at',
                'expires_at',
                'home_telephone',
                'mobile_telephone'
            ]);

        });
    }

    public function down()
    {
        //
    }
}
