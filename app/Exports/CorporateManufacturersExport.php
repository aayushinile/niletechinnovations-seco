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

class CorporateManufacturersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $plants;
    protected $contact_m;


    public function __construct(Collection $plants)
    {
        $this->plants = $plants;
        
    }

    public function collection()
    {
        return $this->plants->map(function ($plant) {
            return [
                'plant_name' => $this->formatPlantName($plant->business_name),
                'full_address' => $plant->full_address,
                'city' => $plant->city,
                'state' => $plant->state,
                'country' => $plant->country,
                'email' => $plant->email,
                'phone' => $this->formatPhone($plant->phone),
                'total_plants' => $this->getPlantsCount($plant->id),
                'plant details' => $this->getPlantsDetails($plant->id),
            ];
        });
    }

    private function formatPlantName($plantName)
    {
        return empty($plantName) ? 'N/A' : $plantName;
    }

    private function formatPhone($phone)
    {
        if (empty($phone)) {
            return 'N/A'; // Return 'N/A' if the phone number is empty
        }

        // Assuming phone numbers should be formatted with a '+1' prefix
        return '+1 ' . $phone;
    }

    private function getSalesManagers($plantId)
    {
        // Fetch the plant record using the manufacturer_id
        $plant = Plant::where('id', $plantId)->first();

        // Check if plant exists
        if (!$plant) {
            return 'N/A'; // Return 'N/A' if the plant is not found
        }

        // Fetch sales managers related to the plant
        $salesManagers = DB::table('plant_sales_manager')
            ->where('plant_id', $plantId)
            ->get(['name', 'email', 'phone', 'designation']);

        if ($salesManagers->isEmpty()) {
            return 'N/A'; // Return 'N/A' if no sales managers found
        }

        // Format each manager's details with line breaks
        $formattedManagers = $salesManagers->map(function ($manager) {
            return "{$manager->name}\n{$manager->email}\n{$manager->phone}\n{$manager->designation}";
        });

        // Join all formatted managers with double line breaks
        return $formattedManagers->implode("\n\n");
    }


    private function getPriceRange($plantId)
    {
        // Fetch the plant record using the manufacturer_id
        $plant = Plant::where('id', $plantId)->first();
        
        // Check if plant exists
        if (!$plant) {
            return 'N/A'; // Return 'N/A' if the plant is not found
        }
         $price_range = $plant->from_price_range .'-'. $plant->to_price_range;
    
        return $price_range ?: 'N/A';
    }


    private function getPlantsCount($plantId)
    {
        // Fetch the plant record using the manufacturer_id
        $plant = Plant::where('manufacturer_id', $plantId)->count();
        
        
         $count = $plant;
    
        return $count ?: 'N/A';
    }


    private function getPlantsDetails($manufacturerId)
{
    // Fetch plants related to the manufacturer
    $plants = Plant::where('manufacturer_id', $manufacturerId)->get(['plant_name', 'email', 'full_address', 'phone']);

    // Check if there are any plants associated with the manufacturer
    if ($plants->isEmpty()) {
        return 'N/A'; // Return 'N/A' if no plants found
    }

    // Format the details for each plant
    $formattedDetails = $plants->map(function ($plant) {
        $name = $plant->plant_name ?: 'N/A';  // Changed from business_name to plant_name
        $email = $plant->email ?: 'N/A';
        $address = $plant->full_address ?: 'N/A';
        $phone = $plant->phone ? '+1 ' . $plant->phone : 'N/A';

        return "{$name}\n{$email}\n{$address}\n{$phone}";
    });

    // Join all formatted plant details with double line breaks
    return $formattedDetails->implode("\n\n");
}

    public function headings(): array
    {
        return [
            'Business Name',
            'Plant Location',
            'City',
            'State',
            'Country',
            'Email',
            'Phone',
            'Total Plants',
            'Plant Details',
        ];
    }


    
    public function registerEvents(): array
{
    return [
        AfterSheet::class => function(AfterSheet $event) {
            // Assuming the plant details are in column I (adjust this if necessary)
            $plantDetailsRange = 'I2:I' . $event->sheet->getHighestRow();
            $event->sheet->getStyle($plantDetailsRange)->getAlignment()->setWrapText(true);
        },
    ];
}



    
}


