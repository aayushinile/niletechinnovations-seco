<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\CommunityPropertyManagers;
use App\Models\CommunityAttributes;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    public function addCommunity(Request $request)
    {
        try {
            // Validate the request data

            $validator = Validator::make($request->all(), [
                'community_name' => 'nullable|string',
                'mobile' => 'nullable|string',
                'email' => 'nullable|email',
                'community_address' => 'nullable|string',
                'latitude' => 'nullable',
                'longtitude' => 'nullable',
                'user_id' => 'nullable|integer',
                'image.*' => 'nullable|image',
                'description' => 'nullable|string',
                'no_of_lots' => 'nullable|integer',
                'no_of_new_homes' => 'nullable|integer',
                'no_of_new_vacant_lots' => 'nullable|integer',
                'no_of_home_needed' => 'nullable|integer',
                'homes_needed_per_year' => 'nullable|integer',
                'property_management' => 'nullable|array',
                'property_management.*.name' => 'nullable|string',
                'property_management.*.designation' => 'nullable|string',
                'property_management.*.phone' => 'nullable|string',
                'property_management.*.email' => 'nullable|email',
                'property_management.*.image' => 'nullable|'
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Create a new community record
            $community = Community::create([
                'community_name' => $request->community_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'community_address' => $request->community_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longtitude,
                'city' => null,
                'state' => null,
                'zipcode' => null,
                'description' => $request->description ?? '',
                'user_id' => $request->user_id,
                'no_of_lots' => $request->no_of_lots,
                'no_of_new_homes' => $request->no_of_new_homes,
                'vacant_lots' => $request->no_of_new_vacant_lots,
                'no_of_home_needed' => $request->no_of_home_needed,
                'homes_needed_per_year' => $request->homes_needed_per_year,
            ]);

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $file) {
                    // Generate unique file name
                    $name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

                    // Move file to destination folder
                    $file->move(public_path('upload/community_image'), $name);

                    CommunityAttributes::create([
                        'community_id' => $community->id,
                        'value' => $name,
                        'attribute_type' => 'community',
                        'attribute_name' => 'Image'
                    ]);
                }
            }

            if ($request->has('property_management') && is_array($request->property_management)) {
                foreach ($request->property_management as $property) {
                    $propertyManager = CommunityPropertyManagers::create([
                        'community_id' => $community->id,
                        'name' => $property['name'],
                        'designation' => $property['designation'],
                        'phone' => $property['phone'],
                        'email_id' => $property['email'],
                        'status' => 1,
                    ]);

                    if (isset($property['image'])) {
                        $file = $property['image'];
                        $imageName = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('upload/property_managers_image'), $imageName);

                        $propertyManager->update(['image' => $imageName]);
                    }
                }
            }

            // Return a response
            return response()->json([
                'message' => 'Community successfully added',
                'community' => $community,
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding the community',
                'error' => $e->getMessage(),
                'status' => false,
            ], 500);
        }
    }


    public function getCommunityDetails(Request $request)
    {
        try {
            $id = $request->id;

            // Fetch community details
            $community = DB::table('community')
                ->where('id', $id)
                ->where('user_id', $request->user_id)
                ->first();

            // Check if community exists
            if (!$community) {
                return response()->json([
                    'message' => 'Community not found',
                    'data' => [],
                    'status' => false
                ], 404);
            }

            // Fetch community images with IDs
            $communityImages = DB::table('community_attributes')
                ->where('community_id', $id)
                ->where('attribute_type', 'community')
                ->select('id', 'value as image') // Select both ID and value
                ->get();

            // Fetch property managers and their images using left join
            $propertyManagers = DB::table('community_property_managers')
                ->where('community_property_managers.community_id', $id)
                ->get();

            // Structure the response data
            $data = [
                'id' => $community->id,
                'user_id' => $request->user_id,
                'community_name' => $community->community_name,
                'mobile' => $community->mobile,
                'email' => $community->email,
                'address' => $community->community_address,
                'description' => $community->description,
                'user_id' => $community->user_id,
                'no_of_lots' => $community->no_of_lots,
                'no_of_new_homes' => $community->no_of_new_homes,
                'vacant_lots' => $community->vacant_lots,
                'no_of_home_needed' => $community->no_of_home_needed,
                'homes_needed_per_year' => $community->homes_needed_per_year,
                'created_at' => $community->created_at,
                'updated_at' => $community->updated_at,
                'images' => $communityImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => asset('upload/community_image/' . $image->image)
                    ];
                })->toArray(),
                'property_management' => $propertyManagers->map(function ($manager) {
                    return [
                        'id' => $manager->id,
                        'name' => $manager->name,
                        'designation' => $manager->designation,
                        'image' => $manager->image ? asset('upload/property_managers_image/' . $manager->image) : null,
                        'email' => $manager->email_id,
                        'phone' => $manager->phone,
                    ];
                })->toArray(),
            ];

            return response()->json([
                'data' => $data,
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the community details',
                'error' => $e->getMessage(),
                'status' => false
            ], 500);
        }
    }



    public function updateCommunity(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:community,id',
                'community_name' => 'nullable|string|',
                'mobile' => 'nullable|string|',
                'email' => 'nullable|string|',
                'community_address' => 'nullable|string|',
                'latitude' => 'nullable',
                'longtitude' => 'nullable',
                'image.*' => 'nullable|image',
                'description' => 'nullable|string',
                'no_of_lots' => 'nullable|',
                'no_of_new_homes' => 'nullable|',
                'no_of_new_vacant_lots' => 'nullable|',
                'no_of_home_needed' => 'nullable|',
                'homes_needed_per_year' => 'nullable|',
                'property_management' => 'nullable|array',
                'property_management.*.name' => 'nullable|string|',
                'property_management.*.designation' => 'nullable|string|',
                'property_management.*.phone' => 'nullable|string|',
                'property_management.*.email' => 'nullable|string|',
                'property_management.*.image' => 'nullable' // Ensure image validation for property managers
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Find the community by id
            $community = Community::findOrFail($request->id);

            // Update the community record
            $community->update([
                'community_name' => $request->community_name ?? $community->community_name,
                'mobile' => $request->mobile ?? $community->mobile,
                'email' => $request->email ?? $community->email,
                'community_address' => $request->community_address ?? $community->community_address,
                'longitude' => $request->longtitude ?? $community->longitude,
                'latitude' =>$request->latitude ?? $community->latitude,
                // Add other fields you want to update similarly
                'description' => $request->description !== null && trim($request->description) !== '' ? $request->description : '',
                'no_of_lots' => $request->no_of_lots ?? $community->no_of_lots,
                'no_of_new_homes' => $request->no_of_new_homes ?? $community->no_of_new_homes,
                'vacant_lots' => $request->no_of_new_vacant_lots ?? $community->vacant_lots,
                'no_of_home_needed' => $request->no_of_home_needed ?? $community->no_of_home_needed,
                'homes_needed_per_year' => $request->homes_needed_per_year ?? $community->homes_needed_per_year,
            ]);

            // Update community images
            if ($request->hasFile('image')) {
                // Delete existing community images
                CommunityAttributes::where('community_id', $community->id)
                    ->where('attribute_type', 'community')
                    ->delete();

                foreach ($request->file('image') as $file) {
                    // Generate unique file name
                    $name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

                    // Move file to destination folder
                    $file->move(public_path('upload/community_image'), $name);

                    CommunityAttributes::create([
                        'community_id' => $community->id,
                        'value' => $name,
                        'attribute_type' => 'community',
                        'attribute_name' => 'Image'
                    ]);
                }
            }

            // Update property management records and their images
            if ($request->has('property_management')) {
                // Delete existing property managers and their images
                CommunityAttributes::where('community_id', $community->id)
                    ->where('attribute_type', 'property_managers')
                    ->delete();

                foreach ($request->property_management as $property) {
                    $propertyManager = CommunityPropertyManagers::create([
                        'community_id' => $community->id,
                        'name' => $property['name'],
                        'designation' => $property['designation'],
                        'phone' => $property['phone'],
                        'email_id' => $property['email'],
                        'status' => 1,
                    ]);

                    if (isset($property['image'])) {
                        $file = $property['image'];
                        $imageName = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('upload/property_managers_image'), $imageName);
                        $propertyManager->update(['image' => $imageName]);
                    }
                }
            }

            // Return a response
            return response()->json([
                'message' => 'Community successfully updated',
                'community' => $community,
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the community',
                'error' => $e->getMessage(),
                'status' => false,
            ], 500);
        }
    }


    public function communityListing(Request $request)
    {
        try {
            $user = $request->user_id;
            $search = $request->search;

            if (empty($user)) {
                return response()->json([
                    'message' => 'No communities found',
                    'data' => [], // or an empty array if preferred
                    'status' => false
                ], 200); // Bad Request
            }

            // Fetch communities along with their attributes
            $query = DB::table('community')
                ->leftJoin('community_attributes', 'community.id', '=', 'community_attributes.community_id')
                ->where('community.user_id', $user)
                ->select(
                    'community.*',
                    'community_attributes.value as attribute_value',
                    'community_attributes.attribute_type as attribute_type',
                    'community_attributes.attribute_name as attribute_name'
                );

            // Apply search filter if provided
            if (!empty($search)) {
                $query->where('community.community_name', 'like', '%' . $search . '%');
            }

            $communities = $query->get();

            // Check if communities exist
            if ($communities->isEmpty()) {
                return response()->json([
                    'message' => 'No communities found for this user',
                    'data' => [],
                    'status' => false
                ], 200);
            }

            $formattedCommunities = [];
            foreach ($communities as $community) {
                $formattedCommunity = (array) $community;
                if ($community->attribute_type == 'community' && $community->attribute_name == 'Image') {
                    $formattedCommunity['image_path'] = asset('upload/community_image/' . $community->attribute_value);
                }

                // Determine state based on latitude and longitude
                if ($community->latitude && $community->longitude) {
                    $state = $this->getStateFromLatLng($community->latitude, $community->longitude);
                    $formattedCommunity["nearby_plants_count"] = $this->getNearestPlants($state, $community->latitude, $community->longitude);
                } else {
                    $formattedCommunity["nearby_plants_count"] = [];
                }

                $formattedCommunities[] = $formattedCommunity;
            }

            // Return the communities with attributes
            return response()->json([
                'message' => 'Communities fetched successfully',
                'communities' => $formattedCommunities,
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the communities',
                'error' => $e->getMessage(),
                'status' => false,
            ], 500);
        }
    }



    public function getStateFromLatLng($lat, $long)
    {
        $mapboxToken = env('MAPBOX_ACESS_TOKEN'); // Get the Mapbox access token from .env file

        // Initialize Guzzle client
        $client = new Client();

        // Fetch state information based on the given latitude and longitude
        $response = $client->request('GET', 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . $long . ',' . $lat . '.json', [
            'query' => [
                'access_token' => $mapboxToken,
                'types' => 'region',
                'limit' => 1,
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        $state = null;

        if (isset($body['features'][0]['text'])) {
            $state = $body['features'][0]['text'];
        }

        return $state;
    }


    public function getNearestPlants($state, $lat, $long)
    {
        $mapboxToken = env('MAPBOX_ACESS_TOKEN'); // Get the Mapbox access token from .env file

        // Initialize Guzzle client
        $client = new Client();

        // Filter plants by state
        $plantsQuery = DB::table('plant')
            ->leftJoin('plant_login', 'plant.manufacturer_id', '=', 'plant_login.id')
            ->select(
                'plant.id as plant_id',
                'plant.latitude as plant_latitude',
                'plant.longitude as plant_longitude',
            )
            ->where('plant.state', $state) // Filter by state
            ->where('plant_login.status', 1);

        // Fetch filtered plants
        $plants = $plantsQuery->get();

        if ($plants->isEmpty()) {
            return [];
        }

        // Structure the response data
        $data = collect([]);

        // Iterate through plants to calculate distance and structure data
        foreach ($plants as $plant) {
            $distance = null;

            if ($lat && $long && $plant->plant_latitude && $plant->plant_longitude) {
                // Make request to Mapbox Directions API
                $response = $client->request('GET', 'https://api.mapbox.com/directions/v5/mapbox/driving/' . $long . ',' . $lat . ';' . $plant->plant_longitude . ',' . $plant->plant_latitude, [
                    'query' => [
                        'access_token' => $mapboxToken,
                        'geometries' => 'geojson',
                        'overview' => 'simplified',
                    ],
                ]);

                $body = json_decode($response->getBody(), true);

                // Extract distance from response
                if (isset($body['routes'][0]['distance'])) {
                    $dis = $body['routes'][0]['distance']; // Distance in meters
                    $distance = round($dis * 0.000621371, 2); // Convert meters to miles
                }
            }

            // Only include plants within 300 miles
            if ($distance !== null && $distance <= 300) {
                $data->push([
                    'plant_id' => $plant->plant_id,
                    // Add more fields if needed
                ]);
            }
        }

        return count($data);
    }

    public function deleteCommunityPhoto(Request $request)
    {
        try {
            // Find the image by ID
            $image = CommunityAttributes::where('id', $request->community_id)->first();
            // dd($image);

            // Delete the image file from the storage
            $imagePath = public_path('upload/community-image/' . $image->image_url);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Delete the image record from the database
            $image->delete();

            // Return a success response
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Handle the error
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }



    public function deletePropertyManagers(Request $request)
    {
        try {
            // Find the image by ID
            $image = CommunityPropertyManagers::where('id', $request->manager_id)->first();
            // dd($image);

            // // Delete the image file from the storage
            // $imagePath = public_path('upload/community-image/' . $image->image_url);
            // if (file_exists($imagePath)) {
            //     unlink($imagePath);
            // }

            // Delete the image record from the database
            $image->delete();

            // Return a success response
            return response()->json([
                'success' => true,
                'status' => true,
            ]);
        } catch (\Exception $e) {
            // Handle the error
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status' => false,
            ]);
        }
    }


    public function deleteSalesManagerPhoto(Request $request)
    {
        try {
            // Find the image by ID
            $id = $request->community_id;
            $image = CommunityPropertyManagers::findOrFail($id);

            // Delete the image file from the storage
            $imagePath = public_path('upload/community-image/' . $image->image_url);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Delete the image record from the database
            $image->image = null;
            $image->save();

            // Return a success response
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Handle the error
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
