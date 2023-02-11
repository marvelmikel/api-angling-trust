<?php

namespace Modules\Voting\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\Services\WPNotification;
use Modules\Members\Entities\Member;
use Modules\Voting\Entities\VotingForm;
use Modules\Voting\Entities\VotingRegistration;
use Modules\Voting\Enums\Notifications;

class SendConfirmations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Member
     */
    protected $member;
    /**
     * @var VotingForm
     */
    protected $form;


    public function __construct(Member $member, VotingForm $form)
    {
        $this->member = $member;
        $this->form = $form;
    }


    public function handle()
    {
        WPNotification::sendCustomerNotification(Notifications::BALLOT_CONFIRMATION, $this->member->user->email);

        $registration = VotingRegistration::query()
            ->where('member_id', $this->member->id)
            ->where('voting_form_id', $this->form->id)
            ->where('registration_intention', 1)
            ->first();

        if($registration) {
            WPNotification::sendCustomerNotification(Notifications::REGISTRATION_INTENT, $this->member->user->email);
        }
    }

}
