<?php

namespace App\Imports;

use App\Models\Plant;
use App\Models\PlantSalesManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\PlantLogin;

use Illuminate\Support\Facades\Log;
class PlantManufacturerImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        // Initialize variables
        $latitude = $longitude = $city = $state = $country = null;

        // Get lat/long, city, state, and country based on the full address using Mapbox
        $address = $row['address']; // This is the 'address' column in the Excel file
        if ($address) {
            $client = new \GuzzleHttp\Client();

            $response = $client->get('https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($address) . '.json', [
                'query' => [
                    'access_token' => 'pk.eyJ1IjoidXNlcnMxIiwiYSI6ImNsdGgxdnpsajAwYWcya25yamlvMHBkcGEifQ.qUy8qSuM_7LYMSgWQk215w',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['features'][0])) {
                $features = $data['features'][0];
                $latitude = $features['geometry']['coordinates'][1] ?? null;
                $longitude = $features['geometry']['coordinates'][0] ?? null;
                $city = $state = $country = '';

                foreach ($features['context'] as $context) {
                    if (strpos($context['id'], 'place') === 0) {
                        $city = $context['text'];
                    } elseif (strpos($context['id'], 'region') === 0) {
                        $state = $context['text'];
                    } elseif (strpos($context['id'], 'country') === 0) {
                        $country = $context['text'];
                    }
                }
            }
        }

        if (!Auth::check()) {
            Log::error('User is not authenticated.');
            return null; // Handle accordingly
        }
        
        $plantName = $row['plant_name'] ?: 'N/A';
        $plantWebsite = $row['plant_website'];
        $aboutOurHomes = $row['about_our_homes'] ?? '--';
        $address = $row['address'];
        $authUser = Auth::user();
        Log::error('Failed to create Plant with data:', ['user_id' => $authUser->id, 'data' => $row]);
        $mfs = PlantLogin::find($authUser->id);

        // Create Plant
        if ($plantName !== 'N/A') {
            $plant = Plant::create([
                'plant_name' => $plantName,
                'phone' => $mfs->phone,
                'email' => $row['corp_con_email'] ?? $mfs->email,
                'web_link' => $plantWebsite,
                'description' => $aboutOurHomes,
                'full_address' => $address,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'manufacturer_id' => $authUser->id,
            ]);

            if (!$plant) {
                Log::error('Failed to create Plant with data: ', $row);
                return null; // Or handle the error as appropriate
            }
            $salesContact = $row['sales_contact'] ?? 'N/A';
            if ($salesContact === 'N/A') {
                Log::warning('Skipping row due to missing sales contact: ', $row);
                return null; // Skip the row if sales contact name is 'N/A'
            }
            // Create Sales Manager
            $salesEmail = $row['sales_contacts_email'] ?? $row['contacts_email'] ?? null;
            $salesPhone = $row['sales_contacts_ph_no'] ?? $row['contacts_ph_no'] ?? null;
            $formattedPhone = $salesPhone !== 'N/A' ? self::formatPhoneNumber($salesPhone) : 'N/A'; // Use self:: to call static method
            PlantSalesManager::create([
                'plant_id' => $plant->id,
                'name' => $salesContact,
                'email' => $salesEmail,
                'phone' => $formattedPhone,
                'designation' => 'Sales Contact',
                'manufacturer_id' => $authUser->id,
            ]);
        }
    }


    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove non-numeric characters
        $cleaned = preg_replace('/\D/', '', $phoneNumber);

        // Check if the cleaned number has the correct length
        if (strlen($cleaned) === 10) {
            // Format the number
            return sprintf('(%s) %s-%s',
                substr($cleaned, 0, 3),
                substr($cleaned, 3, 3),
                substr($cleaned, 6)
            );
        }

        // Return the original if it doesn't match the expected length
        return $phoneNumber;
    }
}

