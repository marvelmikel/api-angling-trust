<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SendExampleEmail extends Command
{
    protected $name = 'core:send-example-email';

    public function handle()
    {
        $path = env('WP_PATH');
        $wp_cli = env('WP_CLI');

        $output = shell_exec("{$wp_cli} --path='{$path}' send_customer_notification example elliot@barques.co.uk");

        \Log::info("Sending Test Email");
        \Log::info($output);
        \Log::info("Test Complete");
    }
}
