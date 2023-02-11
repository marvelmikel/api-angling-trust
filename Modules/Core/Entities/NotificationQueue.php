<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Log;

class NotificationQueue extends Model
{
    protected $table = 'notification_queue';

    protected $guarded = ['id'];

    protected $dates = [
        'sent_at'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function scopeWhereNotSent(Builder $query)
    {
        $query->whereNull('sent_at');
    }

    public function send()
    {
        $map = [
            'cm-password-reset' => 'sendPasswordReset',
            'cm-ticket-purchased' => 'sendTicketPurchase',
            'cm-fishing-draw-entry' => 'sendFishingDrawEntry',
            'cm-ticket-updated' => 'sendTicketUpdated'
        ];

        try {

            if (isset($map[$this->reference])) {
                $method = $map[$this->reference];
                $this->$method();
            } else {
                $this->sendBasic();
            }

            $this->sent_at = now();
            return $this->save();

        } catch (\Exception $exception) {
            return false;
        }
    }

    private function sendBasic()
    {

        if ($this->customer_notification) {
            $this->wpCommand("send_customer_notification {$this->reference} {$this->to}");
        }

        if ($this->admin_notification) {
            $this->wpCommand("send_admin_notification {$this->id}");
        }
    }

    private function sendPasswordReset()
    {

        $token = $this->data['token'];
        $origin = $this->data['origin'];

        $this->wpCommand("send_password_reset {$this->to} {$token} {$origin}");
    }

    private function sendTicketPurchase()
    {

        $id = $this->data['purchased_ticket_id'];

        $this->wpCommand("send_ticket_notification {$id}");
    }

    private function sendTicketUpdated()
    {

        $id = $this->data['purchased_ticket_id'];

        $this->wpCommand("send_updated_event_notification {$id}");
    }

    private function sendFishingDrawEntry()
    {
        $id = $this->data['entry_id'];

        $this->wpCommand("send_fishing_draw_entry {$id}");
    }

    private function wpCommand(string $command): void
    {
        $path = env('WP_PATH');
        $wp_cli = env('WP_CLI');

        $wpCommand = sprintf('%s --path=\'%s\' %s 2>&1', $wp_cli, $path, $command);

        Log::debug(sprintf('Running WP Command: `%s`', $wpCommand));
        try {
            $result = exec($wpCommand, $output, $code);
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
        }

        if ($result !== false && $code === 0) {
            Log::debug(
                sprintf('WP Command success: %s', implode("\n", $output))
            );
        } else {
            Log::error(
                sprintf('WP Command failed: [%d] %s', $code, implode("\n", $output))
            );
        }

    }
}
