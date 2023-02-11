<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DeleteMembersByMembershipNumber extends Command
{
    protected $signature = 'members:delete-members-by-membership-number {filename}';

    public function handle()
    {
        $filename = $this->argument('filename');
        $path = storage_path("data/{$filename}");

        ini_set('auto_detect_line_endings', TRUE);

        $handle = fopen($path, 'r');

        while (($data = fgetcsv($handle)) !== FALSE) {
            $status = $this->deleteMember($data);

            if ($status === 0) {
                $this->error("Failed: {$data[0]}");
            }

            if ($status === 1) {
                $this->info("Deleted: {$data[0]}");
            }

            if ($status === 2) {
                $this->line("Already Deleted: {$data[0]}");
            }
        }

        ini_set('auto_detect_line_endings', FALSE);
        fclose($handle);
    }

    private function deleteMember($data): int
    {
        $reference = $data[0];

        $user = User::withTrashed()
            ->where('reference', $reference)
            ->first();

        $member = Member::withTrashed()
            ->where('user_id', $user->id)
            ->where('membership_type_id', 18)
            ->first();

        if (!$user || !$member) {
            return 0;
        }

        if ($user->trashed() && $member->trashed()) {
            return 2;
        }

        $member->delete();
        $user->delete();

        return 1;
    }
}
