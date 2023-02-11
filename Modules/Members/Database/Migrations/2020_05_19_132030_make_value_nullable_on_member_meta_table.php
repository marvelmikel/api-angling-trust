<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeValueNullableOnMemberMetaTable extends Migration
{
    public function up()
    {
        Schema::table('member_meta', function (Blueprint $table) {
            $table->longText('value')->nullable()->change();
        });
    }

    public function down()
    {
        //
    }
}
