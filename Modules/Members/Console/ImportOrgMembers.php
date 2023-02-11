<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MemberSelectOption;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Entities\MembershipTypeCategory;
use Modules\Members\Repositories\MemberRepository;
use Modules\Members\Services\MembershipReferenceGenerator;
use Modules\Store\Enums\PaymentProvider;

class ImportOrgMembers extends Command
{
    protected $signature = 'members:import-org-members {filename} {skip?}';

    private $count = 0;
    private $imported = 0;
    private $failed = 0;

    private $total = 0;
    private $eta = 'Unknown';

    public function __construct()
    {
        parent::__construct();
    }

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

        $skip = (int)$this->argument('skip') ?? 0;

        for ($i = 0; $i < $skip; $i++) {
            fgetcsv($handle);
        }

        $this->total = $this->total - $skip;

        $this->info("Importing {$this->total} Organisations");
        $this->line('');

        $start = microtime(true);

        while (($data = fgetcsv($handle)) !== FALSE) {
            if ($this->importMember($data)) {
                $this->imported++;
            } else {
                $this->failed++;
            }

            $this->count++;

            if ($this->count % 50 == 0) {
                $this->calculateETA($start, microtime(true));
                $start = microtime(true);
            }

            $this->printProgress();
        }

        $this->info('Import Complete');

        ini_set('auto_detect_line_endings', FALSE);
        fclose($handle);
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
        $this->info('F: ' . $this->failed . ' | S: ' . $this->imported . ' | ETA: ' . $this->eta);
    }

    private function importMember($row)
    {
        Member::unguard();

        $data = [
            'user' => [
                'email' => $row[9],
                'password' => md5(random_bytes(25))
            ],
            'home_telephone' => $row[10],
            'mobile_telephone' => $row[11],
            'address_line_1' => $row[12],
            'address_line_2' => $row[13],
            'address_town' => $row[14],
            'address_county' => $row[15],
            'address_postcode' => $row[16],
            'notes' => $row[81],
            'opt_in_1' => true,
            'opt_in_2' => true,
            'is_imported' => true
        ];

        if ($row[29]) {
            $data['registered_at'] = Carbon::createFromFormat('d/m/Y', $row[29])->format('Y-m-d');
            $data['created_at'] = Carbon::createFromFormat('d/m/Y', $row[29])->format('Y-m-d');
        } else {
            $data['registered_at'] = '1970-07-19 00:00:00';
            $data['created_at'] = '1970-07-19 00:00:00';
        }

        if ($row[50]) {
            $data['membership_pack_sent_at'] = Carbon::createFromFormat('d/m/Y', $row[50])->format('Y-m-d');
        }

        if ($row[31]) {
            $data['renewed_at'] = Carbon::createFromFormat('d/m/Y', $row[31])->format('Y-m-d');
        }

        if ($row[30]) {
            $data['expires_at'] = Carbon::createFromFormat('d/m/Y', $row[30])->format('Y-m-d');
        }

        $membershipType = $this->getMembershipType($row);

        $this->setReference($data, $row, $membershipType);
        $this->setATFLStatus($data, $row);
        $this->setPaymentIsRecurring($data, $row);
        $this->setPaymentProvider($data, $row);

        $meta = [
            'legacy_programme' => 'yes',
            'club_name' => $row[43],
            'primary_contact' => $this->getPrimaryContact($row),
            'facebook_url' => $row[51],
            'instagram_url' => $row[52],
            'twitter_url' => $row[53],
            'website' => $row[54]
        ];

        $this->setFishingRights($meta, $row);
        $this->setDisciplines($meta, $row);

        if (!$member = MemberRepository::import($data, $membershipType)) {
            return false;
        }

        foreach ($meta as $key => $value) {
            if (in_array($key, [])) {
                $member->setMeta($key, $value, 'boolean');
            } elseif (in_array($key, [])) {
                $member->setMeta($key, $value, 'array');
            } else {
                $member->setMeta($key, $value);
            }
        }

        return true;
    }

    private function getMembershipType($row)
    {
        return MembershipType::query()
            ->where('slug', 'lapsed')
            ->first();
    }

    private function setReference(&$data, $row, $membershipType)
    {
        $prefix = MembershipReferenceGenerator::getPrefix($membershipType->slug);

        $data['user']['reference'] = $prefix . $row[27];
    }

    private function setATFLStatus(&$data, $row)
    {
        $data['at_member'] = false;
        $data['fl_member'] = false;
    }

    private function setPaymentIsRecurring(&$data, $row)
    {
        if (in_array($row[25], ['Standing Order', 'Direct Debit'])) {
            $data['payment_is_recurring'] = true;
            return;
        }

        $data['payment_is_recurring'] = false;
    }

    private function setPaymentProvider(&$data, $row)
    {
        if ($row[25] === 'Standing Order') {
            $data['payment_provider'] = PaymentProvider::STRIPE;
            return;
        }

        if ($row[25] === 'Direct Debit') {
            $data['payment_provider'] = PaymentProvider::SMART_DEBIT;
            return;
        }

        $data['payment_provider'] = PaymentProvider::OTHER;
    }

    private function getPrimaryContact($row)
    {
        $data = [
            'title' => null,
            'first_name' => $row[86],
            'last_name' => $row[87],
            'email' => $row[88],
            'home_telephone' => $row[89],
            'mobile_telephone' => $row[90],
            'address_line_1' => $row[91],
            'address_line_2' => $row[92],
            'address_town' => $row[93],
            'address_county' => $row[94],
            'address_postcode' => $row[95]
        ];

        $option = MemberSelectOption::query()
            ->where('type', 'title')
            ->where('name', $row[85])
            ->first();

        if ($option) {
            $data['title'] = $option->slug;
        }

        foreach ($data as $index => $item) {
            if ($item === '0') {
                $data[$index] = null;
            }
        }

        return $data;
    }

    private function getAdditionalContact($row)
    {
        $data = [
            'title' => null,
            'first_name' => $row[97],
            'last_name' => $row[98],
            'email' => $row[99],
            'home_telephone' => $row[100],
            'mobile_telephone' => $row[101],
            'address_line_1' => $row[102],
            'address_line_2' => $row[103],
            'address_town' => $row[104],
            'address_county' => $row[105],
            'address_postcode' => $row[106]
        ];

        $option = MemberSelectOption::query()
            ->where('type', 'title')
            ->where('name', $row[96])
            ->first();

        if ($option) {
            $data['title'] = $option->slug;
        }

        return $data;
    }

    private function setFishingRights(&$meta, $row)
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

        $meta['fishing_rights'] = $fishing_rights;
    }

    private function setDisciplines(&$meta, $row)
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

        $meta['disciplines'] = $disciplines;
    }

    private function setMemberNumbers(&$meta, $row)
    {
        if ($row[73] === '') {
            return;
        }

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

        $meta['member_numbers'] = $member_numbers;
    }
}
