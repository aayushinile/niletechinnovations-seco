<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedPlantMail extends Mailable
{
    use Queueable, SerializesModels;

    public $status;
    public $subject;
    public $plant;

    /**
     * Create a new message instance.
     *
     * @param int $status
     * @param string $subject
     * @param \App\Models\Plant $plant
     */
    public function __construct($status, $subject, $plant)
    {
        $this->status = $status;
        $this->subject = $subject;
        $this->plant = $plant;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.plant_approval')
                    ->with([
                        'status' => $this->status,
                        'plant' => $this->plant,
                    ]);
    }
}
