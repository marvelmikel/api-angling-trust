<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MemberSelectOption;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateORGMemberRecordsFromImport extends ProgressCommand
{
    protected $signature = 'members:update-org-member-records-from-import {filename} {skip?}';

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
            ->where('reference', 'COS' . $reference)
            ->orWhere('reference', 'FI' . $reference)
            ->orWhere('reference', 'TM' . $reference)
            ->orWhere('reference', 'CON' . $reference)
            ->orWhere('reference', 'FED' . $reference)
            ->orWhere('reference', 'CB' . $reference)
            ->orWhere('reference', 'CAG' . $reference)
            ->orWhere('reference', 'AFF' . $reference)
            ->orWhere('reference', 'SFB' . $reference)
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

        $member->title = $this->getTitle($data[1]);
        $member->first_name = $data[2];
        $member->last_name = $data[3];
        $member->home_telephone = $data[5];
        $member->mobile_telephone = $data[6];
        $member->address_line_1 = $data[7];
        $member->address_line_2 = $data[8];
        $member->address_town = $data[9];
        $member->address_county = $data[10];
        $member->address_postcode = $data[11];

        $user->email = $data[4];

        $member->save();
        $user->save();

        return true;
    }

    private function getTitle($value)
    {
        $option = MemberSelectOption::query()
            ->where('type', 'title')
            ->where('name', $value)
            ->first();

        if (!$option) {
            return null;
        }

        return $option->slug;
    }
}
