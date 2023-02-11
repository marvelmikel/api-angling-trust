<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalColumnsToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('type')->after('slug');
            $table->string('department_code')->after('type');
            $table->string('nominal_code')->after('department_code');
            $table->integer('min_age')->nullable()->after('nominal_code');
            $table->integer('max_age')->nullable()->after('min_age');
            $table->boolean('has_pools_payments')->default(0)->after('has_ticket_sales');
            $table->longText('pools_payments')->nullable()->after('has_pools_payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {

        });
    }
}
