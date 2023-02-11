<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CopyReferenceToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $members = \Modules\Members\Entities\Member::all();

        foreach ($members as $member) {
            $user = $member->user;
            if (!$user) {
                continue;
            }
            $user->reference = $member->reference;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
