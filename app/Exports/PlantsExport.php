<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Plant;

class PlantsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $plants;

    public function __construct(Collection $plants)
    {
        $this->plants = $plants;
    }

    public function collection()
    {
        return $this->plants->map(function ($plant) {
            return [
                'plant_name' => $this->formatPlantName($plant->plant_name),
                'full_address' => $plant->full_address,
                'email' => $plant->email,
                'phone' => $this->formatPhone($plant->phone),
                'sales_managers' => $this->getSalesManagers($plant->id),
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
        $plant = Plant::where('manufacturer_id', $plantId)->first();
        
        // Check if plant exists
        if (!$plant) {
            return 'N/A'; // Return 'N/A' if the plant is not found
        }
    
        // Fetch sales managers related to the plant
        $salesManagers = DB::table('plant_sales_manager')
            ->where('plant_id', $plant->id)
            ->get(['name', 'email', 'phone', 'designation']) // Adjust fields as needed
            ->map(function ($manager) {
                // Format each manager's details
                return sprintf(
                    '{%s,%s,%s,%s}',
                    $manager->name,
                    $manager->email,
                    $manager->phone,
                    $manager->designation
                );
            })
            ->implode(', '); // Concatenate all formatted strings
    
        return $salesManagers ?: 'N/A';
    }

    public function headings(): array
    {
        return [
            'Plant Name',
            'Full Address',
            'Email',
            'Phone',
            'Sales Managers',
        ];
    }
}


