<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDestinationToDonationTable extends Migration
{
    const TABLE = 'donations';
    const COLUMN = 'destination';
    const AFTER = 'amount';

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->string(self::COLUMN)->default('any')->after(self::AFTER);
        });
    }

    public function down(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->dropColumn(self::COLUMN);
        });
    }
}
