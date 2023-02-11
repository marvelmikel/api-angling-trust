<?php declare(strict_types=1);

namespace Modules\Store\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Store\Entities\Payment;
use Modules\Store\Services\SmartDebit;

class UpdateSmartDebitPayments extends Command
{
    protected $signature = 'store:update-smart-debit-payments {--days-ago=7} {--date=}';

    private SmartDebit $smartDebit;
    private string $date;

    public function __construct(SmartDebit $smartDebit)
    {
        parent::__construct();

        $this->smartDebit = $smartDebit;
    }

    public function handle()
    {
        $this->date = $this->getDate();

        $this->info(sprintf('Running UpdateSmartDebitPayments for %s', $this->date));

        $response = $this->smartDebit->getPaymentsTakenOn($this->date)->parsed();

        if (!isset($response['Successes'])) {
            $this->info("0 payments were taken on {$this->date}");
            return;
        }

        $successes = $response['Successes'];

        $this->info(sprintf("%s payments were taken on %s", count($successes), $this->date));

        foreach ($successes as $success) {
            $this->markAsCompleted((string) $success->attributes()['reference_number']);
        }
    }

    private function markAsCompleted(string $reference)
    {
        $payment = Payment::query()
            ->where('reference', $reference)
            ->first();

        if (!$payment) {
            $this->warn(sprintf('No payment matching reference \'%s\' found', $reference));
            return;
        }

        if ($payment->completed_at !== null) {
            $this->warn(sprintf(
                'Payment matching reference \'%s\' already marked as completed at \'%s\'',
                $reference,
                $payment->completed_at
            ));

            return;
        }

        $this->info(sprintf('Payment matching reference \'%s\' was completed', $reference));

        $payment->completed_at = $this->date;
        $payment->save();
    }

    private function getDate(): string
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : null;

        if (!$date) {
            $daysAgo = (int) $this->option('days-ago');
            $date = now()->subDays($daysAgo);
        }

        return $date->format('Y-m-d');
    }
}
