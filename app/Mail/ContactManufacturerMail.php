<?php

namespace App\Mail;

use App\Models\ContactManufacturer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactManufacturerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ContactManufacturer $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.contactmanufacturermail')
                    ->subject('New Contact Request from ShowSearch')
                    ->with('contact', $this->contact);
    }
}
