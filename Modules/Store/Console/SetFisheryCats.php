<?php

namespace Modules\Store\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SetFisheryCats extends Command
{
    protected $name = 'store:set-fishery-cats';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $path = storage_path("imports/fish_cats.csv");
        ini_set('auto_detect_line_endings', TRUE);

        $handle = fopen($path, 'r');

        $index = 0;

        while (($data = fgetcsv($handle)) !== FALSE) {
            $this->update($data);
            $this->info($index);
            $index++;
        }
    }

    private function update($data)
    {
        $user = User::query()
            ->where('reference', $data[0])
            ->first();

        if (!$user) {
            throw new \Exception("User not found: {$data[0]}");
        }

        $member = Member::query()
            ->where('user_id', $user->id)
            ->firstOrFail();

        $this->ensureIntegrity($member, $data);

        $categories = [
            'Under £10k' => 52,
            '£10k to £50k' => 53,
            'Over £50k' => 54
        ];

        $category_id = $categories[$data[2]];

        $member->category_id = $category_id;
        $member->save();

        $ten_percent_club = $data[3] === 'Yes';

        if ($ten_percent_club) {
            $member->setMeta('ten_percent_club', true, 'boolean');
        }

        $this->line("$category_id | $ten_percent_club");
    }

    private function ensureIntegrity($member, $data)
    {
        $memberName = trim($member->full_name);
        $importName = trim($data[1]);

        if ($memberName !== $importName) {
            if (in_array($member->id, [29399])) {
                return;
            }

            throw new \Exception("Integrity check failed: '{$memberName}' !== '{$importName}'");
        }

        if (!in_array($data[2], ['Under £10k', '£10k to £50k', 'Over £50k'])) {
            throw new \Exception("Integrity check failed: '{$data[2]}' is not a valid category");
        }
    }
}
