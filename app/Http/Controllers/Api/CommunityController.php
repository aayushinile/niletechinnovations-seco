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
                'user_id' => 'nullable|integer',
                'image.*' => 'nullable|image',
                'description' => 'nullable|string',
                'no_of_lots' => 'nullable|integer',
                'no_of_new_homes' => 'nullable|integer',
                'no_of_new_vacant_lots' => 'nullable|integer',
                'no_of_home_needed' => 'nullable|integer',
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
                'city' => null,
                'state' => null,
                'zipcode' => null,
                'description' => $request->description ?? '',
                'user_id' => $request->user_id,
                'no_of_lots' => $request->no_of_lots,
                'no_of_new_homes' => $request->no_of_new_homes,
                'vacant_lots' => $request->no_of_new_vacant_lots,
                'no_of_home_needed' => $request->no_of_home_needed,
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
                'image.*' => 'nullable|image',
                'description' => 'nullable|string',
                'no_of_lots' => 'nullable|',
                'no_of_new_homes' => 'nullable|',
                'no_of_new_vacant_lots' => 'nullable|',
                'no_of_home_needed' => 'nullable|',
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
                // Add other fields you want to update similarly
                'description' => $request->description !== null && trim($request->description) !== '' ? $request->description : '',
                'no_of_lots' => $request->no_of_lots ?? $community->no_of_lots,
                'no_of_new_homes' => $request->no_of_new_homes ?? $community->no_of_new_homes,
                'vacant_lots' => $request->no_of_new_vacant_lots ?? $community->vacant_lots,
                'no_of_home_needed' => $request->no_of_home_needed ?? $community->no_of_home_needed,
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
                if ($community->latitude && $community->longitude) {

                    $formattedCommunity["nearby_plants_count"] = $this->getNearestPlant($community->latitude, $community->longitude);
                } else {
                    $formattedCommunity["nearby_plants_count"] = 0;
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


    public function getNearestPlant($lat, $long)
    {
        $manufacturersQuery = DB::table('plant_login')
            ->leftJoin('plant', 'plant.manufacturer_id', '=', 'plant_login.id')
            ->leftJoin('plant_sales_manager', 'plant_sales_manager.plant_id', '=', 'plant.id')
            ->leftJoin('plant_media', 'plant_media.plant_id', '=', 'plant.id')
            ->leftJoin('specifications', 'specifications.manufacturer_id', '=', 'plant_login.id')
            ->select(
                'plant.id as plant_id',
                'plant.plant_name as plant_name',
                'plant.phone as plant_phone',
                'plant_sales_manager.name as sales_manager_name',
                'plant_sales_manager.email as sales_manager_email',
                'plant_sales_manager.designation as sales_manager_designation',
                'plant_sales_manager.phone as sales_manager_phone',
                'plant_sales_manager.image as sales_manager_image',
                'plant.full_address as plant_location',
                'plant.type as plant_type',
                'plant.price_range as plant_price_range',
                'plant.latitude as plant_latitude',
                'plant.longitude as plant_longitude',
                'plant_media.image_url as plant_image_url'
            )
            ->where('plant.manufacturer_id', '!=', null)
            ->where('plant_login.status', 1);


        // Fetch manufacturers
        $manufacturers = $manufacturersQuery->orderBy('plant_login.id', 'DESC')->get();

        if ($manufacturers->isEmpty()) {
            return 0;
        }

        // Structure the response data
        $data = collect([]);
        $apiKey = env('GOOGLE_API_KEY');

        // Get filter inputs
        $latitude = $lat;
        $longitude = $long;


        // Initialize Guzzle client
        $client = new Client();
        // Iterate through manufacturers to calculate distance and structure data
        foreach ($manufacturers as $manufacturer) {
            $distance = null;
            if ($latitude && $longitude && $manufacturer->plant_latitude && $manufacturer->plant_longitude) {
                // Make request to Google Maps Distance Matrix API
                $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/distancematrix/json', [
                    'query' => [
                        'key' => $apiKey,
                        'origins' => $latitude . ',' . $longitude,
                        'destinations' => $manufacturer->plant_latitude . ',' . $manufacturer->plant_longitude,
                        'units' => 'imperial', // 'imperial' for miles
                    ],
                ]);

                $body = json_decode($response->getBody(), true);
                // dd($body);
                // Extract distance from response
                if (isset($body['rows'][0]['elements'][0]['distance']['text'])) {
                    $distanceText = $body['rows'][0]['elements'][0]['distance']['text'];
                    $dis = $body['rows'][0]['elements'][0]['distance']['value'];
                    // dd($distanceText);
                    if (strpos($distanceText, 'mi') !== false) {
                        $distance = floatval(str_replace(' mi', '', $distanceText));
                        $distanceText = str_replace(' mi', ' Miles', $distanceText); // Replace ' mi' with ' Miles'
                    } else {
                        // Convert km to miles if necessary
                        $distanceValue = floatval(str_replace(' km', '', $distanceText));
                        $distance = round($distanceValue * 0.621371, 2);
                        $distanceText = $distance . ' Miles';
                    }
                }
            }

            // Only include manufacturers within 50 miles
            if ($dis !== null && $dis <= 400000) {

                // Check if the manufacturer ID already exists in $data
                $existing = $data->where('plant_id', $manufacturer->plant_id)->first();

                if (!$existing) {

                    $data->push([
                        'plant_id' => $manufacturer->plant_id,

                        'distance' => $distanceText, // Distance in miles
                    ]);
                }
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
