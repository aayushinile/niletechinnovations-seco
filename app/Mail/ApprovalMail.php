<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $plantname;
    public $location;
    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($plantname,$location,$type)
    {
        $this->plantname = $plantname;
        $this->location = $location;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Pending for Approval')
                    ->view('emails.approval-mail')
                    ->with([
                        'plant_name' => $this->plantname,
                        'location' => $this->location,
                        'type' => $this->type,
                    ]);
    }
}