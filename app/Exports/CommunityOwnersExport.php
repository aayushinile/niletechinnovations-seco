<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Plant;
use App\Models\Specifications;
use App\Models\ContactManufacturer;

class CommunityOwnersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $owners;

    public function __construct(Collection $owners)
    {
        $this->owners = $owners;
    }

    public function collection()
    {
        return $this->owners->map(function ($owner) {
            return [
                $owner->fullname,
                $owner->business_name,
                $owner->email,
                $owner->mobile,
                $owner->business_address,
                'contacted_plants' => $this->getContactedOwners($owner->id),
                 'type' => $owner->type == 1 ? 'Retail Sales Lot' : 'Community Owner'
            ];
        });
    }

    private function getContactedOwners($ownerId)
    {
        // Fetch the plants contacted by the owner
        $manufracturers = ContactManufacturer::where("user_id", $ownerId)->pluck("plant_id")->toArray();
        $contact_m = DB::table('plant')
            ->select('plant.*', 'plant_media.image_url')
            ->leftJoin('plant_media', 'plant.id', '=', 'plant_media.plant_id')
            ->whereIn('plant.id', $manufracturers)
            ->get();

        // Check if contacts exist
        if ($contact_m->isEmpty()) {
            return 'N/A'; // Return 'N/A' if no contacts found
        }

        // Format each plant's details with name on the first line and address on the second line
        $formattedContacts = $contact_m->map(function ($contact) {
            return sprintf(
                "%s\n%s\n%s",
                $contact->plant_name,
                $contact->full_address,
                $contact->email
            );
        });

        // Join all formatted contacts with double line breaks
        return $formattedContacts->implode("\n\n");
    }

    public function headings(): array
    {
        return [
            'Community/Retailer Name',
            'Business Name',
            'Email',
            'Phone',
            'Business Address',
            'Contacted Plants',
            'Type'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Enable wrap text for the 'Contacted Plants' column (e.g., column D)
                $contactedPlantsRange = 'D2:D' . $event->sheet->getHighestRow();
                $event->sheet->getStyle($contactedPlantsRange)->getAlignment()->setWrapText(true);
            },
        ];
    }
}
