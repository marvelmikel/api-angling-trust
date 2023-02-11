<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateIMMemberRecordsFromImport extends ProgressCommand
{
    protected $signature = 'members:update-im-member-records-from-import {filename} {skip?}';

    public function handle()
    {
        $filename = $this->argument('filename');
        $path = storage_path("imports/{$filename}");

        ini_set('auto_detect_line_endings', TRUE);

        $handle = fopen($path, 'r');

        $total = 0;

        while(!feof($handle)){
            fgets($handle);
            $total++;
        }

        fclose($handle);

        $this->setTotal($total);

        $handle = fopen($path, 'r');

        $skip = (int) $this->argument('skip') ?? 0;

        for ($i = 0; $i < $skip; $i++) {
            fgetcsv($handle);

            $this->handleItem(function() {
                return true;
            });
        }

        $this->start();

        while (($data = fgetcsv($handle)) !== FALSE) {
            $this->handleItem(function() use ($data) {
                return $this->updateMemberRecord($data);
            });
        }

        $this->stop();

        fclose($handle);

        ini_set('auto_detect_line_endings', FALSE);
    }

    private function updateMemberRecord($data)
    {
        $reference = $data[0];

        $user = User::withTrashed()
            ->where('reference', $reference)
            ->first();

        if (!$user) {
            return false;
        }

        $member = Member::withTrashed()
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            return false;
        }

        if ($data[1] === 'TRUE') {
            $member->opt_in_1 = 1;
        } else {
            $member->opt_in_1 = 0;
        }

        if ($data[2] === 'TRUE') {
            $member->opt_in_2 = 1;
        } else {
            $member->opt_in_2 = 0;
        }

        $member->notes = $data[3];

        return $member->save();
    }
}
