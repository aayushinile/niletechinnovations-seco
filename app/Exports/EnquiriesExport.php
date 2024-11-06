<?php

namespace App\Exports;

use App\Models\Plant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class EnquiriesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $contact_m;

    public function __construct(Collection $contact_m)
    {
        $this->contact_m = $contact_m;
    }

    public function collection()
    {
        return $this->contact_m->map(function ($item) {
            // Fetch plant name from plant table
            $plant = Plant::find($item->plant_id);
            $plantName = $plant ? $plant->plant_name : "N/A";

            // Format the created_at date to MM/DD/YYYY
            $formattedDate = Carbon::parse($item->created_at)->format('m/d/Y');

            return [
                $plantName,
                $item->user_name,
                $item->email,
                $item->phone_no === '+1' ? 'N/A' : $item->phone_no,
                $item->company_name,
                $item->message,
                'type' => $this->getType($item),
                $formattedDate,
            ];
        });
    }

    private function getType($item)
    {

        // Get the type of the plant
        $type = $item->type;

        // Map the type to the corresponding description
        switch ($type) {
            case 1:
                return 'Retailer';
            case 2:
                return 'Community Owner';
            default:
                return 'Community Owner';
        }
    }

    public function headings(): array
    {
        return [
            'Plant Name',
            'CO/Retailer Name',
            'Email',
            'Phone',
            'Company Name',
            'Message',
            'Type',
            'Enquiry Date',
        ];
    }
}



