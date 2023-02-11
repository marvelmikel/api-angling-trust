<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotesToDonationTable extends Migration
{
    const TABLE = 'donations';
    const COLUMN = 'note';
    const AFTER = 'destination';

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->text(self::COLUMN)->after(self::AFTER);
        });
    }

    public function down(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->dropColumn(self::COLUMN);
        });
    }
}
