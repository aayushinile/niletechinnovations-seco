<?php

namespace App\Exports;

use App\Models\ContactManufacturer;
use App\Models\Plant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ManufacturerEnquiriesExport implements FromCollection, WithHeadings, ShouldAutoSize
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
            $enq = ContactManufacturer::find($item->enquiry_id);
            $plant = Plant::where('id',$enq->plant_id)->first();
            $plantName = $plant ? $plant->plant_name : "N/A";

            // Format the created_at date to MM/DD/YYYY
            $formattedDate = Carbon::parse($item->created_at)->format('m/d/Y');

            return [
                $plantName,
                $item->enquiry_name,
                $item->enquiry_mail,
                $item->enquiry_phone === '+1' ? 'N/A' : $item->enquiry_phone,
                $item->company_name ?? 'N/A',
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
