<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ProgressCommand extends Command
{
    private $count = 0;
    private $success = 0;
    private $failure = 0;

    private $total;

    private $start;
    private $eta = 'Unknown';

    protected function setTotal($total)
    {
        $this->total = $total;
    }

    protected function start()
    {
        $this->info("Processing {$this->total} Items");
        $this->line('');

        $this->start = microtime(true);
    }

    protected function stop()
    {
        $this->info('Processing Complete');
    }

    protected function handleItem(callable $callback)
    {
        $result = $callback();

        if ($result) {
            $this->success++;
        } else {
            $this->failure++;
        }

        $this->count++;

        if ($this->count % 50 == 0) {
            $this->calculateETA($this->start, microtime(true));
            $this->start = microtime(true);
        }

        $this->printProgress();
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
}
