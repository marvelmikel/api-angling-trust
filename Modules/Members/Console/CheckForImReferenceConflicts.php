<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Core\Console\ProgressCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckForImReferenceConflicts extends ProgressCommand
{
    protected $signature = 'members:check-for-im-reference-conflicts {filename}';

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

        $this->start();

        while (($data = fgetcsv($handle)) !== FALSE) {
            $this->handleItem(function() use ($data) {
                $result = User::query()
                    ->where('reference', $data[29])
                    ->exists();

                return !$result;
            });
        }

        $this->stop();

        fclose($handle);

        ini_set('auto_detect_line_endings', FALSE);
    }
}
