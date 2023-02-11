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
use Modules\Store\Enums\PaymentProvider;

class ImportIMMembers extends Command
{
    protected $signature = 'members:import-im-members {filename} {skip?}';

    private $count = 0;
    private $imported = 0;
    private $failed = 0;

    private $total = 0;
    private $eta = 'Unknown';

    private $membershipType;

    public function handle()
    {
        $this->membershipType = MembershipType::query()
            ->where('slug', 'coach')
            ->firstOrFail();

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

        $this->info("Importing {$this->total} Members");
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

        $existingUser = User::withTrashed()
            ->where('reference', $row[29])
            ->first();

        if ($existingUser) {
            $existingMember = Member::withTrashed()
                ->where('user_id', $existingUser->id)
                ->first();

            $existingMember->forceDelete();
            $existingUser->forceDelete();
        }

        $data = [
            'user' => [
                'email' => $row[7],
                'password' => md5(random_bytes(25)),
                'reference' => $row[29],
            ],
            'first_name' => $row[2],
            'last_name' => $row[3],
            'home_telephone' => $row[8],
            'mobile_telephone' => $row[9],
            'address_line_1' => $row[10],
            'address_line_2' => $row[11],
            'address_town' => $row[12],
            'address_county' => $row[13],
            'address_postcode' => $row[14],
            'opt_in_1' => true,
            'opt_in_2' => true,
            'is_imported' => true,
            'registered_at' => Carbon::createFromFormat('d/m/Y', $row[31])->format('Y-m-d'),
            'created_at' => Carbon::createFromFormat('d/m/Y', $row[31])->format('Y-m-d')
        ];

        if (substr($row[32], strlen($row[32]) - 4) !== '2080') {
            $data['expires_at'] = Carbon::createFromFormat('d/m/Y', $row[32])->format('Y-m-d');
        }

        $this->setPaymentIsRecurring($data, $row);
        $this->setPaymentProvider($data, $row);
        $this->setCategoryId($data, $row);
        $this->setRenewedAt($data, $row);
        $this->setAtFL($data, $row);
        $this->setTitle($data, $row);

        $meta = [
            'legacy_programme' => 'yes',
            'coaching_level' => $row[57],
            'qualified' => $row[58],
            'safeguarding_training_expires_at' => $row[60],
            'dbs_number' => $row[61],
            'dbs_expires_at' => $row[62],
            'first_aid_training_expires_at' => $row[63],
            'coach_number' => $row[64]
        ];

        $this->setDateOfBirth($meta, $row);
        $this->setGender($meta, $row);
        $this->setEthnicity($meta, $row);
        $this->setDisciplines($meta, $row);
        $this->setDisability($meta, $row);
        $this->setReasonForJoining($meta, $row);
        $this->setOptOut($meta, $row);

        if (!$member = MemberRepository::import($data, $this->membershipType)) {
            return false;
        }

        foreach ($meta as $key => $value) {
            if (in_array($key, ['raffle_opt_out'])) {
                $member->setMeta($key, $value, 'boolean');
            } elseif (in_array($key, ['disciplines', 'date_of_birth'])) {
                $member->setMeta($key, $value, 'array');
            } else {
                $member->setMeta($key, $value);
            }
        }

        return true;
    }

    private function setPaymentIsRecurring(&$data, $row)
    {
        if (in_array($row[27], ['Standing Order', 'Direct Debit'])) {
            $data['payment_is_recurring'] = true;
            return;
        }

        $data['payment_is_recurring'] = false;
    }

    private function setPaymentProvider(&$data, $row)
    {
        if ($row[27] === 'Standing Order') {
            $data['payment_provider'] = PaymentProvider::STRIPE;
            return;
        }

        if ($row[27] === 'Direct Debit') {
            $data['payment_provider'] = PaymentProvider::SMART_DEBIT;
            return;
        }

        $data['payment_provider'] = PaymentProvider::OTHER;
    }

    private function setCategoryId(&$data, $row)
    {
        $category = MembershipTypeCategory::query()
            ->where('membership_type_id', $this->membershipType->id)
            ->where('slug', 'standard')
            ->first();

        $data['category_id'] = $category->id;
    }

    private function setRenewedAt(&$data, $row)
    {
        if ($row[33] !== '') {
            $data['renewed_at'] = Carbon::createFromFormat('d/m/Y', $row[33])->format('Y-m-d');
        }
    }

    private function setAtFL(&$data, $row)
    {
        $data['at_member'] = true;
        $data['fl_member'] = false;
    }

    private function setTitle(&$data, $row)
    {
        $option = MemberSelectOption::query()
            ->where('type', 'title')
            ->where('name', $row[1])
            ->first();

        if (!$option) {
            return;
        }

        $data['title'] = $option->slug;
    }

    private function setDateOfBirth(&$meta, $row)
    {
        if ($row[6] === '') {
            return;
        }

        $date_of_birth = Carbon::createFromFormat('d/m/Y', $row[6]);

        $meta['date_of_birth'] = [
            'day' => $date_of_birth->format('d'),
            'month' => $date_of_birth->format('m'),
            'year' => $date_of_birth->format('Y')
        ];
    }

    private function setGender(&$meta, $row)
    {
        if ($row[4] === 'Male') {
            $meta['gender'] = 'male';
        }

        if ($row[4] === 'Female') {
            $meta['gender'] = 'female';
        }
    }

    private function setEthnicity(&$meta, $row)
    {
        if ($row[5] === 'White or White British') {
            $meta['ethnicity'] = 'white-british';
        }
    }

    private function setDisciplines(&$meta, $row)
    {
        $disciplines = [];

        if ($row[15] !== '') {
            $disciplines[] = 'match';
        }

        if ($row[16] !== '') {
            $disciplines[] = 'carp';
        }

        if ($row[17] !== '') {
            $disciplines[] = 'game';
        }

        if ($row[18] !== '') {
            $disciplines[] = 'coarse';
        }

        if ($row[19] !== '') {
            $disciplines[] = 'sea';
        }

        $meta['disciplines'] = $disciplines;
    }

    private function setDisability(&$meta, $row)
    {
        if ($row[21] === 'TRUE') {
            $meta['disability_1'] = 'yes';
        }
    }

    private function setReasonForJoining(&$meta, $row)
    {
        if ($row[23] !== '') {
            $meta['reason_for_joining'] = 'other';
            $meta['reason_for_joining_other'] = $row[23];
        }
    }

    private function setOptOut(&$meta, $row)
    {
        if ($row[39] === 'TRUE') {
            $meta['raffle_opt_out'] = 1;
        } else {
            $meta['raffle_opt_out'] = 0;
        }
    }
}
