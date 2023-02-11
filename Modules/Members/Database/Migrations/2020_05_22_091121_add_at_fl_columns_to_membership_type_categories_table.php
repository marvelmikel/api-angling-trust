<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAtFlColumnsToMembershipTypeCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('membership_type_categories', function (Blueprint $table) {
            $table->boolean('at_member')->default(0)->after('slug');
            $table->boolean('fl_member')->default(0)->after('at_member');
        });
    }

    public function down()
    {
        //
    }
}
