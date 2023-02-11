<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPriceColumnFromMembershipTypesTable extends Migration
{
    public function up()
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropColumn([
                'price'
            ]);
        });
    }

    public function down()
    {
        //
    }
}
