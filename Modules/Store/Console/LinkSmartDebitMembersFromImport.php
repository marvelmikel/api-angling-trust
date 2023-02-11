<?php

namespace Modules\Store\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LinkSmartDebitMembersFromImport extends Command
{
    protected $signature = 'store:link-smart-debit-members-from-import {filename}';

    public function handle()
    {
        $filename = $this->argument('filename');
        $path = storage_path("imports/{$filename}");

        ini_set('auto_detect_line_endings', TRUE);

        $handle = fopen($path, 'r');

        $linked = 0;
        $total = 0;

        while (($data = fgetcsv($handle)) !== FALSE) {
            if ($this->linkMember($data)) {
                $linked++;
            }

            $total++;
        }

        ini_set('auto_detect_line_endings', FALSE);
        fclose($handle);

        $this->info("Linked {$linked}/{$total}");
    }

    private function linkMember($data)
    {
        if ($data[2] === '') {
            return false;
        }

        $member = Member::query()
            ->where('id', $data[0])
            ->firstOrFail();

        $user = $member->user;

        if ($user->reference !== $data[1]) {
            throw new \InvalidArgumentException("{$member->id}, reference does not match: {$data[1]}");
        }

        $user->smart_debit_id = $data[2];
        $user->save();

        return true;
    }
}
