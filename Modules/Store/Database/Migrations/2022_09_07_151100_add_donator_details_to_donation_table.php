<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDonatorDetailsToDonationTable extends Migration
{
    const TABLE = 'donations';
    const COLUMN__NAME = 'name';
    const COLUMN__EMAIL = 'email';
    const AFTER = 'member_id';

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->string(self::COLUMN__NAME)->default('')->after(self::AFTER);
            $table->string(self::COLUMN__EMAIL)->default('')->after(self::COLUMN__NAME);
        });
    }

    public function down(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->dropColumn(self::COLUMN__NAME);
            $table->dropColumn(self::COLUMN__EMAIL);
        });
    }
}
