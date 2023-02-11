<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowIfAttendingToVotingFormsQuestionsTable extends Migration
{
    private const TABLE = 'voting_form_questions';
    private const COLUMN = 'show_if_attending';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean(self::COLUMN)->nullable()->default(null);
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn(self::COLUMN);
        });
    }
}
