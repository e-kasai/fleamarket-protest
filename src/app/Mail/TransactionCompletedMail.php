<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class TransactionCompletedMail extends Mailable
{
    public $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function build()
    {
        return $this->subject('取引が完了しました')
            ->view('email.transaction_completed')
            ->with([
                'transaction' => $this->transaction
            ]);
    }
}
