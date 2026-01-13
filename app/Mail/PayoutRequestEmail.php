<?php

namespace App\Mail;

use App\Models\EventPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayoutRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $payout;

    public function __construct(EventPayout $payout)
    {
        $this->payout = $payout;
    }

    public function build()
    {
        return $this->subject("Payout Request - {$this->payout->payout_number}")
                    ->markdown('emails.payout-request');
    }
}
