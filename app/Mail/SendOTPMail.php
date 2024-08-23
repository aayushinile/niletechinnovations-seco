<?php
  
namespace App\Mail;
  
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
  
class SendOTPMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    public $subject;

    public function __construct($mailData, $subject)
    {
        $this->mailData = $mailData;
        $this->subject = $subject;
    }

    public function build()
    {
        $mailData = $this->mailData;
        return $this->subject($this->subject)
                    ->view('manufacturer.sendotpmail', compact('mailData'));
    }
}