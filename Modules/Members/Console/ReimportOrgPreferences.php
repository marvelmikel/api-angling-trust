<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Services\MembershipReferenceGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ReimportOrgPreferences extends Command
{
    protected $signature = 'members:reimport-org-preferences {filename} {skip?}';

    private $count = 0;
    private $success = 0;
    private $failure = 0;

    private $total = 0;

    private $eta = 'Unknown';

    public function handle()
    {
        $filename = $this->argument('filename');
        $path = storage_path("imports/{$filename}");

        ini_set('auto_detect_line_endings', TRUE);

        $handle = fopen($path, 'r');

        while(!feof($handle)){
            fgets($handle);
            $this->total++;
        }

        fclose($handle);

        $handle = fopen($path, 'r');

        $skip = (int) $this->argument('skip') ?? 0;

        for ($i = 0; $i < $skip; $i++) {
            fgetcsv($handle);
        }

        $this->total = $this->total - $skip;

        $this->info("Updating {$this->total} Organisations");
        $this->line('');

        $start = microtime(true);

        while (($data = fgetcsv($handle)) !== FALSE) {
            if ($this->updateMember($data)) {
                $this->success++;
            } else {
                $this->failure++;
            }

            $this->count++;

            if ($this->count % 50 == 0) {
                $this->calculateETA($start, microtime(true));
                $start = microtime(true);
            }

            $this->printProgress();
        }

        $this->info('Update Complete');
    }

    private function calculateETA($start, $stop)
    {
        $elapsed = $stop - $start;
        $elapsed_per_item = $elapsed / 50;

        $items_left = $this->total - $this->count;
        $time_left = $items_left * $elapsed_per_item;

        $eta = time() + $time_left;

        $this->eta = date('H:i:s', $eta) . ' (' . round($elapsed_per_item, 3) . ' spi)';
    }

    private function printProgress()
    {
        $this->output->write("\033[1A");
        $this->output->write("                                                  \n");
        $this->output->write("\033[1A");
        $this->info('F: ' . $this->failure . ' | S: ' . $this->success . ' | ETA: ' . $this->eta);
    }

    private function updateMember($row)
    {
        $membershipType = $this->getMembershipType($row);
        $prefix = MembershipReferenceGenerator::getPrefix($membershipType->slug);

        $reference = $prefix . $row[27];

        $user = User::query()
            ->where('reference', $reference)
            ->first();

        if (!$user) {
            return false;
        }

        $member = Member::query()
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            return false;
        }

        $this->setFishingRights($member, $row);
        $this->setDisciplines($member, $row);

        return true;
    }

    private function setFishingRights($member, $row)
    {
        $value = $row[47];
        $fishing_rights = [];

        if (strpos($value, 'OWN') !== false) {
            $fishing_rights[] = 'own';
        }

        if (strpos($value, 'LEASE') !== false) {
            $fishing_rights[] = 'lease';
        }

        if (strpos($value, 'LICENSE') !== false) {
            $fishing_rights[] = 'licence';
        }

        $member->setMeta('fishing_rights', $fishing_rights);
    }

    private function setDisciplines($member, $row)
    {
        $disciplines = [];

        if ($row[67] === 'TRUE') {
            $disciplines[] = 'game';
        }

        if ($row[68] === 'TRUE') {
            $disciplines[] = 'coarse';
        }

        if ($row[69] === 'TRUE') {
            $disciplines[] = 'sea';
        }

        if ($row[70] === 'TRUE') {
            $disciplines[] = 'recreation';
        }

        if ($row[71] === 'TRUE') {
            $disciplines[] = 'match';
        }

        if ($row[72] === 'TRUE') {
            $disciplines[] = 'specimen';
        }

        $member->setMeta('disciplines', $disciplines);
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
}
