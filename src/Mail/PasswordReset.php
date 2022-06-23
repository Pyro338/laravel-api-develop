<?php

namespace Gamebetr\Api\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Reset token.
     * @var string
     */
    protected $token;

    /**
     * User.
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $user;

    /**
     * Constructor.
     * @param string $token
     * @param \Illuminate\Contracts\Auth\Authenticatable
     * @return void
     */
    public function __construct(string $token, Authenticatable $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    /**
     * Build.
     * @return self
     */
    public function build()
    {
        return $this->from($this->user->domain->email)
                    ->to($this->user->email)
                    ->markdown('api::emails.passwordReset', [
                        'token' => $this->token,
                        'user' => $this->user,
                        'url' => URL::temporarySignedRoute('confirmreset', Carbon::now()->addMinutes(30), ['token' => $this->token]),
                    ]);
    }
}
