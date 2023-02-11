<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceRecurringToMembershipTypeCategories extends Migration
{
    private const TABLE =  'membership_type_categories';
    private const COLUMN__PRICE = 'price';
    private const COLUMN__PRICE_RECURRING = 'price_recurring';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->unsignedInteger(self::COLUMN__PRICE_RECURRING)
                ->after(self::COLUMN__PRICE)
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn(self::COLUMN__PRICE);
        });
    }
}
