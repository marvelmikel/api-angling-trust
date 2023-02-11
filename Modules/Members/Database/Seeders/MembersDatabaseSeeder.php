<?php

namespace Modules\Members\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Members\Database\Seeders\Member\MemberSeeder;

class MembersDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(MemberSeeder::class);
    }
}
