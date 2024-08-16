<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class ContactedManufacturerExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $contact_m;
    protected $owner;

    public function __construct($contact_m, $owner)
    {
        $this->contact_m = $contact_m;
        $this->owner = $owner;
    }

    public function collection()
    {
        return $this->contact_m->map(function ($item) {
            return [
                'community_owner' => $this->owner->business_name, // Use owner's business name
                'name' => $item->plant_name,
                'location' => $item->full_address,
                'email' => $item->email,
                'phone' => $this->formatPhone($item->phone),
                'message' =>  $this->getMessage($this->owner->id, $item->id),
                'date' => $this->getDate($this->owner->id, $item->id),
            ];
        });
    }

    private function formatPhone($phone)
    {
        // Check if phone number is not empty
        if (!empty($phone)) {
            // Add +1 prefix if it is not already present
            if (strpos($phone, '+1') === false) {
                return '+1 ' . $phone;
            }
            return $phone;
        }
        return 'N/A';
    }



    private function getMessage($user_id, $plant_id)
    {
        // Retrieve the message from ContactManufacturer where user_id and plant_id match
        $contactManufacturer = \App\Models\ContactManufacturer::where('user_id', $user_id)
            ->where('plant_id', $plant_id)
            ->first();

        return $contactManufacturer ? $contactManufacturer->message : 'N/A';
    }



    private function getDate($user_id, $plant_id)
    {
        // Retrieve the message from ContactManufacturer where user_id and plant_id match
        $contactManufacturer = \App\Models\ContactManufacturer::where('user_id', $user_id)
            ->where('plant_id', $plant_id)
            ->first();

            return $contactManufacturer ? $contactManufacturer->created_at->format('m/d/Y') : 'N/A';
    }

    public function headings(): array
    {
        return [
            'Community Owner',
            'Plant Name',
            'Location',
            'Email',
            'Phone',
            'Message',
            'Enquiry Date',
        ];
    }
}



