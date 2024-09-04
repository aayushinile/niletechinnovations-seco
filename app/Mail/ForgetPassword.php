<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $name;
    public function __construct($user, $token,$name)
    {
        $this->user = $user;
        $this->token = $token;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Forget Password OTP')
                    ->view('manufacturer.forgetpasswordmail')
                    ->with([
                        'user' => $this->user,
                        'token' => $this->token,
                        'name' => $this->name,
                    ]);
    }
}
