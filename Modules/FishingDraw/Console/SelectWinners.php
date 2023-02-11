<?php

namespace Modules\FishingDraw\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\FishingDraw\Entities\FishingDraw;
use Modules\FishingDraw\Entities\FishingDrawEntry;
use Modules\FishingDraw\Entities\FishingDrawPrize;
use Modules\FishingDraw\Entities\FishingDrawWinner;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SelectWinners extends Command
{
    protected $signature = 'fishing-draw:select-winners {draw_id}';

    public function handle()
    {
        $draw = FishingDraw::findOrFail($this->argument('draw_id'));
        $prizes = $draw->prizes;

        $results = [];

        foreach ($prizes as $prize) {
            $results[] = [
                'prize' => $prize,
                'winner' => $this->selectWinnerForPrize($draw, $prize)
            ];
        }

        $this->sendNotification($draw, $results);
    }

    private function selectWinnerForPrize(FishingDraw $draw, FishingDrawPrize $prize)
    {
        $winner = FishingDrawWinner::query()
            ->where('draw_id', $draw->id)
            ->where('prize_id', $prize->id)
            ->first();

        if ($winner) {
            return $winner;
        }

        $entries = FishingDrawEntry::query()
            ->where('draw_id', $draw->id)
            ->where('prize_id', $prize->id)
            ->get();

        $bag = collect();

        foreach ($entries as $entry) {
            for ($i = 1; $i <= $entry->quantity; $i++) {
                $bag->push($entry->id);
            }
        }

        $winner = new FishingDrawWinner();

        $winner->draw_id = $draw->id;
        $winner->prize_id = $prize->id;
        $winner->entry_id = $bag->random();
        $winner->save();

        return $winner;
    }

    private function sendNotification(FishingDraw $draw, array $results)
    {
        $text = '';
        $text .= $draw->name . " Results \n\n";

        foreach ($results as $result) {
            $entry = $result['winner']->entry;

            $text .= $result['prize']->name . "\n";
            $text .= "Reference: {$entry->reference} \n";
            $text .= "Name: {$entry->name} \n";
            $text .= "Email: {$entry->email} \n";

            $text .= "\n\n";
        }

        Mail::raw($text, function($message) use ($draw) {
            $message->to('Samantha.Frost-Jones@anglingtrust.net')
            ->subject($draw->name . " Results");
        });
    }
}
