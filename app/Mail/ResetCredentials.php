<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $password;
    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password, $email)
    {
        $this->password = $password;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Updated Credentials')
                    ->view('emails.reset_credentials')
                    ->with([
                        'password' => $this->password,
                        'email' => $this->email,
                    ]);
    }
}