<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCategoryIdColumnNullable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });
    }

    public function down()
    {
        //
    }
}
