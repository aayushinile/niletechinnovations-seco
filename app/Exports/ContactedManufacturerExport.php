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

    public function headings(): array
    {
        return [
            'Community Owner',
            'Name',
            'Location',
            'Email',
            'Phone',
        ];
    }
}



