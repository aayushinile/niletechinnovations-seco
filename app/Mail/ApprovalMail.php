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
    public $business_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($plantname,$location,$type,$business_name)
    {
        $this->plantname = $plantname;
        $this->location = $location;
        $this->type = $type;
        $this->business_name= $business_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Plant Creation By Manufacturer')
                    ->view('emails.approval-mail')
                    ->with([
                        'plant_name' => $this->plantname,
                        'location' => $this->location,
                        'type' => $this->type,
                        'business_name' => $this->business_name,
                    ]);
    }
}