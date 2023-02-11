<?php

namespace Modules\Members\Database\Seeders\Member;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Members\Entities\Member;
use Modules\Members\Repositories\MemberRepository;

class MemberSeeder extends Seeder
{
    public function run()
    {
        /** @var array $members */
        $members = factory(Member::class, (int) $this->command->ask('Number of Members', 0))->raw();

        Model::reguard();

        foreach ($members as $data) {
            MemberRepository::create($data);
        }
    }
}
