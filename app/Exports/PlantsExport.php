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

class PlantsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
                'plant_name' => $this->formatPlantName($plant->plant_name),
                'full_address' => $plant->full_address,
                'city' => $plant->city,
                'state' => $plant->state,
                'country' => $plant->country,
                'email' => $plant->email,
                'phone' => $this->formatPhone($plant->phone),
                'price_range' => $this->getPriceRange($plant->id),
                'shipping cost' => $this->getShippingCost($plant->id),
                'type' => $this->getType($plant->id),
                'sales_managers' => $this->getSalesManagers($plant->id),
               
                'contacted_owners' => $this->getContactedOwners($plant->id),
                'specifications' => $this->getSpecifications($plant->id),
                'created_at' => $this->formatDate($plant->created_at),
            ];
        });
    }

    private function formatPlantName($plantName)
    {
        return empty($plantName) ? 'N/A' : $plantName;
    }

    private function formatDate($date)
    {
        // Format date to MM/DD/YYYY
        return $date ? \Carbon\Carbon::parse($date)->format('m/d/Y') : 'N/A';
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
            // Format the phone number with a +1 prefix if it's not empty
            $formattedPhone = !empty($manager->phone) ? '+1 ' . $manager->phone : 'N/A';
            return "{$manager->name}\n{$manager->email}\n{$formattedPhone}\n{$manager->designation}";
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


    private function getContactedOwners($plantId)
    {
        // Get the contact manufacturer entries for the current plant
        $manufacturers = ContactManufacturer::where("plant_id", $plantId)->get();

        // Extract user IDs from the manufacturers
        $userIds = $manufacturers->pluck('user_id')->toArray();

        // Fetch the business names and types from the users table based on the user IDs
        $userDetails = DB::table('users')
            ->whereIn('id', $userIds)
            ->get(['id', 'business_name', 'type']); // Fetch user ID, business name, and type

        // Create a map of user IDs to business names and types
        $businessNameMap = $userDetails->pluck('business_name', 'id')->toArray();
        $typeMap = $userDetails->pluck('type', 'id')->toArray();

        // Check if manufacturers exist
        if ($manufacturers->isEmpty()) {
            return 'N/A'; // Return 'N/A' if no manufacturers found
        }

        // Format each contact's details with name, type, and business name
        $formattedContacts = $manufacturers->map(function ($manufacturer) use ($businessNameMap, $typeMap) {
            // Get the business name and type using the user ID
            $businessName = $businessNameMap[$manufacturer->user_id] ?? 'N/A'; // Default to 'N/A' if not found
            $userType = $typeMap[$manufacturer->user_id] ?? null;

            // Determine the type description
            if (is_null($manufacturer->user_id)) {
                $typeDescription = "Anonymous";
            } elseif ($userType == 1) {
                $typeDescription = "Retailer";
            } elseif ($userType == 2) {
                $typeDescription = "Community Owner";
            } else {
                $typeDescription = "Anonymous";
            }

            return sprintf(
                "%s\n%s\n%s\n%s\n%s\n%s",  // Added type description
                $businessName,              // Business name from users table
                $typeDescription,           // Type (Retailer, Community Owner, etc.)
                $manufacturer->location,
                $manufacturer->user_name,   // Assuming user_name is in ContactManufacturer
                $manufacturer->email,       // Assuming user_email is in ContactManufacturer
                $manufacturer->phone_no     // Assuming phone_no is in ContactManufacturer
            );
        });

        // Join all formatted contacts with double line breaks
        return $formattedContacts->implode("\n\n");
    }


    private function getShippingCost($plantId)
    {
        // Fetch the plant record using the manufacturer_id
        $plant = Plant::where('id', $plantId)->first();
        
        // Check if plant exists
        if (!$plant) {
            return 'N/A'; // Return 'N/A' if the plant is not found
        }
        $shippingCost = DB::table('shipping_cost')
        ->where('type', $plant['type'])
        ->first();
         $shipping_cost= $shippingCost->shipping_cost;
    
        return $shipping_cost ?: 'N/A';
    }



    private function getPlantType($plantId)
    {
        // Fetch the plant record using the manufacturer_id
        $plant = Plant::where('id', $plantId)->first();
        
        // Check if plant exists
        
        $type = DB::table('plant_login')
        ->where('id', $plant->manufacturer_id)
        ->first();
        if (!$type) {
            return 'N/A'; // Return 'N/A' if the plant is not found
        }
         $plant_type= $type->plant_type;
    
         switch ($plant_type) {
            case 'plant_rep':
                return 'Plant Representative';
            case 'corp_rep':
                return 'Corporate Representative';
            default:
                return 'N/A';
        }
    }



    private function getType($plantId)
    {
        // Fetch the plant record using the manufacturer_id
        $plant = Plant::where('id', $plantId)->first();
        
        // Check if plant exists
        if (!$plant) {
            return 'N/A'; // Return 'N/A' if the plant is not found
        }

        // Get the type of the plant
        $type = $plant->type;

        // Map the type to the corresponding description
        switch ($type) {
            case 'sw':
                return 'Single Wide';
            case 'dw':
                return 'Double Wide';
            case 'sw_dw':
                return 'Single Wide and Double Wide';
            default:
                return 'N/A';
        }
    }



    private function getSpecifications($plantId)
    {
        // Fetch the plant record using the plantId
        $plant = Plant::find($plantId);
        
        // Check if plant exists
        if (!$plant) {
            return 'N/A'; // Return 'N/A' if the plant is not found
        }
        
        // Get the comma-separated specification IDs from the plant record
        $specificationIds = $plant->specification;
        
        // Check if specificationIds is not empty
        if (empty($specificationIds)) {
            return 'N/A'; // Return 'N/A' if no specifications are found
        }
        
        // Convert comma-separated IDs into an array
        $specificationIdsArray = explode(',', $specificationIds);
        
        // Fetch the specifications from the specifications table using these IDs
        $specifications = Specifications::whereIn('id', $specificationIdsArray)->get();
        
        // Check if specifications exist
        // if ($specifications->isEmpty()) {
        //     return 'N/A'; // Return 'N/A' if no specifications are found
        // }
        
        // Map the specifications to names and values
        $specificationsArray = $specifications->map(function ($specification) {
            return '{' . $specification->name . ': ' . $specification->values . '}';
        });
        
        // Join the specifications with a comma and space
        return $specificationsArray->implode(', ');
    }

    public function headings(): array
    {
        return [
            'Plant Name',
            'Plant Location',
            'City',
            'State',
            'Country',
            'Email',
            'Phone',
            'Price Range ($)',
            'Shipping Cost ($)',
            'Type',
            'Sales Personnel',
            'Contacted Community Owners',
            'Specifications',
            'Date',
        ];
    }


    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Enable wrap text for the 'Sales Personnel' column (e.g., column K)
                $salesManagersRange = 'K2:K' . $event->sheet->getHighestRow();
                $event->sheet->getStyle($salesManagersRange)->getAlignment()->setWrapText(true);

                // Enable wrap text for the 'Contacted Community Owners' column (e.g., column L)
                $contactedOwnersRange = 'L2:L' . $event->sheet->getHighestRow();
                $event->sheet->getStyle($contactedOwnersRange)->getAlignment()->setWrapText(true);
            },
        ];
    }



    
}


