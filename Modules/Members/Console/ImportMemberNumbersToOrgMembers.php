<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Services\MembershipReferenceGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportMemberNumbersToOrgMembers extends Command
{
    protected $signature = 'members:import-member-numbers-to-org-members {skip?}';

    private $count = 0;

    public function handle()
    {
        $path = storage_path('imports/org.csv');

        ini_set('auto_detect_line_endings', TRUE);

        $this->info('Importing Member Numbers');
        $this->info('');

        $handle = fopen($path, 'r');

        $skip = (int) $this->argument('skip') ?? 0;

        for ($i = 0; $i < $skip; $i++) {
            fgetcsv($handle);
        }

        while (($data = fgetcsv($handle)) !== FALSE) {
            $this->importMemberNumbers($data);
            $this->count++;
            $this->printProgress();
        }

        ini_set('auto_detect_line_endings', FALSE);
        fclose($handle);
    }

    private function printProgress()
    {
        $this->output->write("\033[1A");
        $this->info($this->count);
    }

    private function getMembershipType($row)
    {
        $slug = null;

        switch ($row[0]) {
            case 'Consultatives & River Associations':
                $slug = 'consultatives';
                break;
            case 'Affiliate Member':
                $slug = 'affiliate';
                break;
            case "CAAG's":
                $slug = 'caag';
                break;
            case 'Charter Boats':
                $slug = 'charter-boat';
                break;
            case 'Clubs':
                $slug = 'club-or-syndicate';
                break;
            case 'Federations':
                $slug = 'federation';
                break;
            case 'Fishery':
                $slug = 'fishery';
                break;
            case 'Trade members':
                $slug = 'trade-member';
                break;
            case 'Salmon Fishery Board':
                $slug = 'salmon-fishery-board';
                break;
        }

        $membershipType = MembershipType::query()
            ->where('slug', $slug)
            ->first();

        if (!$membershipType) {
            throw new \Exception('Could not find membership type with slug: ' . $slug);
        }

        return $membershipType;
    }

    private function importMemberNumbers($row)
    {
        if ($row[73] === '') {
            return;
        }

        $membershipType = $this->getMembershipType($row);

        $prefix = MembershipReferenceGenerator::getPrefix($membershipType->slug);
        $reference = $prefix . $row[27];

        $user = User::query()
            ->where('reference', $reference)
            ->firstOrFail();

        $member = Member::query()
            ->where('user_id', $user->id)
            ->firstOrFail();

        $member_numbers = [
            'junior' => [
                'male' => $row[73],
                'female' => $row[74]
            ],
            'senior' => [
                'male' => $row[75],
                'female' => $row[76]
            ],
            'veteran' => [
                'male' => $row[77],
                'female' => $row[78]
            ],
            'disabled' => [
                'male' => $row[79],
                'female' => $row[80]
            ]
        ];

        $member->setMeta('member_numbers', $member_numbers);
    }
}
