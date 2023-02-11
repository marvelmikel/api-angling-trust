<?php

namespace Modules\Members\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Members\Entities\Member;

class MemberRegisteredEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $member;

    public function __construct(Member $member)
    {
        $this->subject = 'New Member';

        $this->member = $member;
    }

    public function build()
    {
        return $this->markdown('members::mail.notification.member-registered', [
            'member' => $this->member
        ]);
    }
}
