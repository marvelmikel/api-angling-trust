<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogoUrlToEventCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('event_categories', function (Blueprint $table) {
            $table->string('logo_url')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('event_categories', function (Blueprint $table) {
            $table->dropColumn(['logo_url']);
        });
    }
}
