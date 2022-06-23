<?php

namespace Gamebetr\Api\Mail;

use Carbon\Carbon;
use Gamebetr\Api\Models\PaybetrWithdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ConfirmWithdrawal extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Reset token.
     * @var string
     */
    protected $token;

    /**
     * Withdrawal.
     * @var \Gamebetr\Api\Models\PaybetrWithdrawal
     */
    protected $withdrawal;

    /**
     * Constructor.
     * @param string $token
     * @param \Gamebetr\Api\Models\Withdrawal
     * @return void
     */
    public function __construct(string $token, PaybetrWithdrawal $withdrawal)
    {
        $this->token = $token;
        $this->withdrawal = $withdrawal;
    }

    /**
     * Build.
     * @return self
     */
    public function build()
    {
        return $this->from($this->withdrawal->player->domain->email)
                    ->to($this->withdrawal->player->email)
                    ->markdown('api::emails.confirmWithdrawal', [
                        'token' => $this->token,
                        'withdrawal' => $this->withdrawal,
                        'url' => URL::temporarySignedRoute('confirmwithdrawal', Carbon::now()->addMinutes(30), ['uuid' => $this->withdrawal->uuid, 'token' => $this->token]),
                        'cancelUrl' => URL::temporarySignedRoute('cancelwithdrawal', Carbon::now()->addMinutes(30), ['uuid' => $this->withdrawal->uuid, 'token' => $this->token]),
                    ]);
    }
}
