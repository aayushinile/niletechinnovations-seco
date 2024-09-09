<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\ContactManufacturer;
use App\Models\ShippingCost;
use App\Models\Otp;
use App\Models\Plant;
use App\Models\Specifications;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Mail;
use App\Mail\SendOTPMail;
use App\Mail\ContactManufacturerMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'fullname' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'mobile' => 'nullable|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:255',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required_with:password|same:password',
            'status' => 'nullable|string|max:10',
            'no_of_communities_sales_lot' => 'nullable',
            'no_of_mhs' => 'nullable',
            'device_token' => 'nullable|string',
            'type' => 'nullable|integer|in:1,2',
        ], [
            'email.unique' => 'This email is already registered with Show Search.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password_confirmation.same' => 'Password confirmation does not match the password.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => false,
            ], 200);
        }

        // Create a new user
        $user = User::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'business_name' => $request->business_name,
            'business_address' => $request->business_address,
            'business_city' => null,
            'business_state' => null,
            'business_zipcode' => null,
            'community_owner' => null,
            'location' => null,
            'mailverified' => true,
            'password' => Hash::make($request->password),
            'status' => $request->status ?? '1',
            'no_of_communities' => $request->no_of_communities_sales_lot,
            'no_of_mhs' => $request->no_of_mhs,
            'device_token' => $request->device_token,
            'type' => $request->type ?? 2, //1-retailer,2-community owner
        ]);
        $token = $user->createToken('seco')->plainTextToken;
        // Return a response
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'user_status' => '1',
            'status' => true,
            'token' => $token,
        ], 200);
    }

    public function login(Request $request)
    {
        // Validate the login request
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => false,
            ], 200);
        }

        // Attempt to find the user
        $user = User::where('email', $request->email)->first();
        // dd($user);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials', 'status' => false], 200);
        }
        if ($user->status == 0) {
            return response()->json([
                'message' => 'Your account has been deactivated by the admin',
                'status' => false,
            ], 200);
        }elseif($user->status == 2){
            return response()->json([
                'message' => 'This email is not registered with Show Search',
                'status' => false,
            ], 200);
        }
        // Create a new personal access token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the token and user information
        return response()->json([
            'message' => 'User successfully logged in',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'status' => true,
        ]);
    }



    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        // Return a response
        return response()->json([
            'message' => 'Successfully logged out',
            'status' => true,
        ]);
    }

    public function userDetails(Request $request)
    {
        try {
            $user = Auth::user();
            // $latitude = $request->input('latitude');
            // $longitude = $request->input('longitude');
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 200);
            }
            $contacted_manufacturer = DB::table('contact_manufacturer')
                ->select([
                    'contact_manufacturer.*',
                    'plant.id as plant_id',
                    'plant.plant_name',
                    'plant.phone',
                    'plant.full_address',
                    'plant.type',
                    'plant.price_range',
                    'plant.latitude',
                    'plant.longitude',
                    'plant_media.image_url'
                ])
                ->leftJoin('plant', 'plant.id', '=', 'contact_manufacturer.plant_id')
                ->leftJoin('plant_media', 'plant.id', '=', 'plant_media.plant_id')
                ->where('contact_manufacturer.user_id', $user->id)
                ->get();
            $uniqueManufacturers = $contacted_manufacturer->unique('plant_id')->values();
            // Process the results
            $processedManufacturers = $uniqueManufacturers->map(function ($manufacturer) {
                $manufacturer->image_url = !empty($manufacturer->image_url)
                    ? asset('upload/manufacturer-image/' . $manufacturer->image_url)
                    : null;
                return $manufacturer;
            });

            $userLocations = DB::table('locations')
            ->where('user_id', $user->id)
            ->get();
            // Create an array of objects format for user details
            $userDetails =
                (object) [
                    'id' => $user->id,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'mobile' => !empty($user->mobile) && strtolower($user->mobile) !== 'null' ? $user->mobile : null,
                    'business_name' => $user->business_name ?? 'N/A',
                    'business_address' => $user->business_address ?? 'N/A',
                    'business_city' => $user->business_city ?? 'N/A',
                    'business_state' => $user->business_state ?? 'N/A',
                    'business_zipcode' => $user->business_zipcode ?? 'N/A',
                    'community_owner' => $user->community_owner ?? 'N/A',
                    'location' => $user->location ?? 'N/A',
                    'mailverified' => $user->mailverified ?? 'N/A',
                    'no_of_mhs' => $user->no_of_mhs ?? 'N/A',
                    'no_of_communities' => $user->no_of_communities ?? 'N/A',
                    'status' => $user->status ?? 'N/A',
                    'device_token' => $user->device_token ?? 'N/A',
                    'type' => $user->type == 1 ? 'Retail Sales Lot' : 'Community Owner',
                    'profile_image' => $user->image ? asset('upload/profile-image/' . $user->image) : asset('images/defaultuser.png'),
                    'company_image' => $user->company_image ? asset('upload/profile-image/' . $user->company_image) : asset('images/defaultuser.png'),
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            return response()->json([
                'user' => $userDetails,
                'contacted_manufacturers' => $processedManufacturers,
                'locations' => $userLocations,
                'status' => true,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving user details',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    private function getDistances($userLat, $userLng, $manufacturers)
    {
        $apiKey = 'YOUR_GOOGLE_API_KEY'; // Replace with your Google API key
        $origins = $userLat . ',' . $userLng;
        $destinations = $manufacturers->map(function ($manufacturer) {
            return $manufacturer->latitude . ',' . $manufacturer->longitude;
        })->join('|');

        $response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
            'origins' => $origins,
            'destinations' => $destinations,
            'key' => $apiKey,
        ]);

        $data = $response->json();

        $distances = [];
        if (isset($data['rows']) && isset($data['rows'][0]['elements'])) {
            foreach ($data['rows'][0]['elements'] as $index => $element) {
                if ($element['status'] === 'OK') {
                    $distances[$manufacturers[$index]->id] = $element['distance']['text'];
                }
            }
        }

        return $distances;
    }


    public function contactManufacturer(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'user_name' => 'nullable|string|max:255',
                'email' => 'nullable|string',
                'phone_no' => 'nullable|string',
                'manufacturer_id' => 'nullable|string|max:255',
                'message' => 'nullable|string',
                'plant_id' => 'nullable',
                'location' => 'nullable',
                'user_id' => 'nullable',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ], 200);
            }

            // Create a new contact manufacturer record
            $user = $request->user_id;
            $contact = ContactManufacturer::create([
                'user_name' => $request->user_name,
                'manufacturer_id' => $request->manufacturer_id,
                'message' => $request->message,
                'user_id' => $user ?? '',
                'plant_id' => $request->plant_id,
                'phone_no' => $request->phone_no,
                'location' => $request->location,
                'email' => $request->email,
                'status' => 0,
            ]);

            try {
                $plant = Plant::where('id',$request->plant_id)->first();
                // Send email to the plant
                $plantEmail = $plant->email;// Replace with actual plant email or retrieve from the database
                    // dd($plantEmail);
                Mail::to($plantEmail)->send(new ContactManufacturerMail($contact));
            } catch (\Exception $e) {
                // Handle any errors during the email sending
                return response()->json([
                    'message' => 'Contact request submitted, but failed to send email',
                    'status' => true,
                    'contact' => $contact,
                    'email_error' => $e->getMessage(),
                ], 201);
            }

            // Return a response
            return response()->json([
                'message' => 'Contact request successfully submitted',
                'status' => true,
                'contact' => $contact,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while submitting the contact request',
                'status' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'fullname' => 'nullable|string|max:255',
                'mobile' => 'nullable|string|max:255',
                'business_name' => 'nullable|string|max:255',
                'business_address' => 'nullable|string|max:255',
                'business_city' => 'nullable|string|max:255',
                'business_state' => 'nullable|string|max:255',
                'business_zipcode' => 'nullable|string|max:255',
                'community_owner' => 'nullable|boolean',
                'no_of_mhs' =>'nullable',
                'no_of_communities' => 'nullable',
                'location' => 'nullable|integer|in:1,2,3,4',
                'status' => 'nullable|string|max:10',
                'image' => 'nullable',
                'company_image' => 'nullable',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ], 200);
            }

            // Update the authenticated user's profile
            $user = Auth::user();
            if(!$user){
                return response()->json([
                    'message' => 'This email is not registered with Show Search',
                    'status' => false,
                ], 200);
            }
            
            $user->fill($request->except(['password', 'device_token']));
            

            if ($request->hasFile('image')) {
                if ($user->image) {
                    // Define the image path
                    $imagePath = public_path('upload/profile-image/' . $user->image);
    
                    // Check if the file exists and delete it
                    if (file_exists($imagePath)) {
                        unlink($imagePath); // Delete the old image file
                    }
                }
    
                // Upload the new profile image
                $file = $request->file('image');
                $imageName = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('upload/profile-image'), $imageName);
                $user->image = $imageName; // Update the user's profile image field
            }
    
            // Handle the company image
            if ($request->hasFile('company_image')) {
                if ($user->company_image) {
                    // Define the company image path
                    $companyImagePath = public_path('upload/profile-image/' . $user->company_image);
    
                    // Check if the file exists and delete it
                    if (file_exists($companyImagePath)) {
                        unlink($companyImagePath); // Delete the old company image file
                    }
                }
    
                // Upload the new company image
                $companyFile = $request->file('company_image');
                $companyImageName = 'COMP_IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $companyFile->getClientOriginalExtension();
                $companyFile->move(public_path('upload/profile-image'), $companyImageName);
                $user->company_image = $companyImageName; // Update the user's company image field
            }
            // if ($request->has('mobile') && !is_null($request->mobile) && $request->mobile !== '') {
            //     // Prepend +1 to the mobile number
            //     $user->mobile = '+1' . $request->mobile;
            // }
            $user->save();

            // Return a response
            return response()->json([
                'message' => 'Profile updated successfully ',
                'user' => $user,
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function deleteProfileImage(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Check if the user has an image
            if ($user->image) {
                // Define the image path
                $imagePath = public_path('upload/profile-image/' . $user->image);

                // Check if the file exists and delete it
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the old image file
                }

                // Remove the image reference from the database
                $user->image = null;
                $user->save();

                // Return a success response
                return response()->json([
                    'message' => 'Profile image deleted successfully',
                    'status' => true,
                ], 200);
            } else {
                // Return a response indicating no image was found
                return response()->json([
                    'message' => 'No profile image found',
                    'status' => false,
                ], 404);
            }
        } catch (\Exception $e) {
            // Return an error response
            return response()->json([
                'message' => 'An error occurred while deleting profile image',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function change_password(Request $request)
    {
        try {
            $data = array();
            $new_password = $request->new_password;
            $old_password = $request->old_password;
            if (!empty($old_password)) {
                $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'old_password' => 'required|min:8',
                    'new_password' => 'required|min:8',
                    'confirm_password' => 'required|same:new_password',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'new_password' => 'required|min:8',
                    'confirm_password' => 'required|same:new_password',
                ]);
            }

            if ($validator->fails()) {
                $errorMessages = $validator->errors()->all(); // Get all error messages as an array
                $combinedErrors = implode(', ', $errorMessages); // Combine the messages into a single string
                return response()->json([
                    'status' => false,
                    'message' => $combinedErrors // Return combined errors as a message
                ], 200);
            }

            if (!empty($old_password)) {
                /*Checking old password is same or not */
                $userdata = User::where('email', $request->email)->where('status', 1)->first();
                if (!Hash::check($request->old_password, $userdata->password)) {
                    $arr = array("status" => false, "message" => "Check your old password.");
                    return response()->json($arr);
                }
            }

            $update = array('password' => Hash::make($new_password));
            $id = User::where('email', $request->email)->where('status', 1)->update($update);
            if (!empty($id)) {
                //$user = $userdata;
                $data['status'] = true;
                $data['message'] = "Password changed successfully";
                return response()->json($data);
            } else {
                $data['status'] = false;
                $data['message'] = 'Your account has been deactivated by the admin';
                return response()->json($data);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // public function getNotification(Request $request)
    // {
    //     try {
    //         $user = Auth::user();
    //         //dd($user);
    //         $user_id = $request->user_id;
    //         if($user != null){
    //             $notifications = Notification::where('user_id', $user->id)->where('status',0)->orderBy('id','DESC')->get();
    //         }else{
    //             $notifications = [];
    //         }


    //         $success['notifications'] = $notifications;
    //         return response()->json(["status" => true, "message" => "Notifications", "data" => $success]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'An error occurred!',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }



    // public function ClearNotification(Request $request)
    // {
    //     try {

    //         $validator = Validator::make($request->all(), [
    //             'clear' => 'nullable|boolean'
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json(['error' => $validator->errors()], 400);
    //         }
    //         $validated = $validator->validated();
    //         $user = Auth::user();
    //         if($user){
    //             $notifications = Notification::where('user_id',$user->id)->where('status',0)->get();

    //         if($validated['clear'] == 1){
    //             foreach($notifications as $val){
    //                 $val->update(['status'=> 1]);
    //             }

    //         }
    //         $message = 'Notifications Cleared Successfully';
    //     }else{
    //         $message = 'No notifications!';
    //     }
    //         return response()->json(["status" => true, "message" => $message]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'An error occurred!',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getLocations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user_id;

        try {
            // Check if the user_id is provided and is not empty
            if (empty($userId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'User ID is required',
                    'data' => []
                ], 200);
            }

            // Fetch locations based on user_id
            $locations = DB::table('locations')->where('user_id', $userId)->orderby('id','desc')->get();

            if ($locations->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No locations found',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Locations retrieved successfully',
                'data' => ['locations' => $locations]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred!',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }



    public function deleteLocation(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user_id;
        $locationId = $request->id;

        try {
            // Check if the location exists for the given user_id and id
            $location = DB::table('locations')
            ->where('id', $locationId)
                ->where('user_id', $userId)
                ->first();

            if (!$location) {
                return response()->json([
                    'status' => false,
                    'message' => 'Location not found or does not belong to the user',
                    'data' => []
                ], 200);
            }

            // Delete the location
            DB::table('locations')
                ->where('user_id', $userId)
                ->where('id', $locationId)
                ->delete();

            return response()->json([
                'status' => true,
                'message' => 'Location deleted successfully',
                'data' => []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred!',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


    public function LocationDetails(Request $request)
    {
        try {
            // Retrieve the location based on the provided ID
            $location = DB::table('locations')->where('id', $request->id)->first();
            
            // Check if location exists
            if (!$location) {
                return response()->json(["status" => false, "message" => "Location not found!"], 404);
            }
            
            // Call the manufacturerListing function with the retrieved latitude and longitude
            return $this->manufacturerListing(new Request([
                'latitude' => $location->latitude,
                'longitude' => $location->longitude
            ]));

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred!',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function saveLocationDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'user_id' => 'nullable|integer',
            'unsave' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $lat = $request->lat;
        $long = $request->long;
        $locationName = $request->location_name;
        $userId = $request->user_id;
        $unsave = $request->unsave;


        if (empty($userId)) {
            return response()->json(['status' => false, 'message' => 'Please log in first', 'data' => []], 200);
        }

        if (empty($userId)) {
            return response()->json(['status' => false, 'message' => 'User ID is required', 'data' => []], 200);
        }
        try {



            
            $existingLocation = DB::table('locations')
            ->where('user_id', $userId)
            ->where('latitude', $lat)
            ->where('longitude', $long)
            ->first();


            if ($unsave) {
                if ($existingLocation) {
                    DB::table('locations')
                        ->where('user_id', $userId)
                        ->where('latitude', $lat)
                        ->where('longitude', $long)
                        ->delete();
    
                    return response()->json(['status' => true, 'message' => 'Location unsaved successfully', 'data' => []], 200);
                } else {
                    return response()->json(['status' => false, 'message' => 'Location not found to unsave', 'data' => []], 404);
                }
            }

            if ($existingLocation) {
                return response()->json([
                    'status' => false,
                    'message' => 'Location already saved',
                    'data' => []
                ], 200);
            }
            // Fetch location details from Mapbox
            $mapboxToken = env('MAPBOX_ACESS_TOKEN');
            $response = Http::get("https://api.mapbox.com/geocoding/v5/mapbox.places/{$long},{$lat}.json", [
                'access_token' => $mapboxToken
            ]);

            if ($response->successful()) {
                $data = json_decode($response->getBody(), true);
                $features = $data['features'][0];

                // Extract city, state, and country from the response
                $city = $features['text'] ?? ''; // Use the main text as city if context doesn't have it
                $state = '';
                $country = '';

        

                if ($features) {
                    foreach ($features['context'] as $context) {
                        if (strpos($context['id'], 'place') === 0) {
                            $city = $context['text'];
                        } elseif (strpos($context['id'], 'region') === 0) {
                            $state = $context['text'];
                        } elseif (strpos($context['id'], 'country') === 0) {
                            $country = $context['text'];
                        }
                    }

                    // Save the location details
                    $locationData = [
                    'location' => $locationName,
                    'latitude' => $lat,
                    'longitude' => $long,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'user_id' => $userId,
                    'created_at'=> Carbon::now(),
                ];

                // Save the location details
                DB::table('locations')->insert($locationData);

                    return response()->json(['status' => true, 'message' => 'Location details saved successfully','data' => $locationData]);
                } else {
                    return response()->json(['status' => false, 'message' => 'Location not found'], 404);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Mapbox API request failed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred!',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function plantListing(Request $request)
    {
        try {
            $plants = DB::table('plant')
                ->leftJoin('manufacturer', 'plant.manufacturer_id', '=', 'manufacturer.id')
                ->leftJoin('plant_sales_manager', 'plant.id', '=', 'plant_sales_manager.plant_id')
                ->leftJoin('plant_media', 'plant.id', '=', 'plant_media.plant_id')
                ->leftJoin('specifications', 'plant.specification', '=', 'specifications.id')
                ->select(
                    'plant.id',
                    'plant.plant_name',
                    'plant.email',
                    'plant.phone',
                    'plant.description',
                    'plant.full_address',
                    'plant.zipcode',
                    'plant.price_range',
                    'plant.type',
                    'manufacturer.full_name as manufacturer_full_name',
                    'manufacturer.email as manufacturer_email',
                    'manufacturer.mobile as manufacturer_mobile',
                    'manufacturer.manufacturer_name',
                    'plant_sales_manager.name as sales_manager_name',
                    'plant_sales_manager.phone as sales_manager_phone',
                    'plant_sales_manager.email as sales_manager_email',
                    'plant_sales_manager.designation as sales_manager_designation',
                    'plant_media.image_url as plant_image_url',
                    'specifications.name as specification_name',
                    'specifications.values as specification_value'
                )
                ->orderBy('plant.id', 'DESC')
                ->get()
                ->groupBy('id');

            $plantList = $plants->map(function ($plantGroup) {
                $plant = $plantGroup->first();
                return [
                    'id' => $plant->id,
                    'plant_name' => $plant->plant_name,
                    'email' => $plant->email,
                    'phone' => $plant->phone,
                    'description' => $plant->description,
                    'full_address' => $plant->full_address,
                    'zipcode' => $plant->zipcode,
                    'price_range' => $plant->price_range,
                    'type' => $plant->type,
                    'manufacturer_full_name' => $plant->manufacturer_full_name,
                    'manufacturer_email' => $plant->manufacturer_email,
                    'manufacturer_mobile' => $plant->manufacturer_mobile,
                    'manufacturer_name' => $plant->manufacturer_name,
                    'sales_managers' => $plantGroup->map(function ($item) {
                        return [
                            'name' => $item->sales_manager_name,
                            'phone' => $item->sales_manager_phone,
                            'email' => $item->sales_manager_email,
                            'designation' => $item->sales_manager_designation,
                        ];
                    })->unique(),
                    'media' => $plantGroup->map(function ($item) {
                        return [
                            'image_url' => $item->plant_image_url,
                        ];
                    })->unique(),
                    'specifications' => $plantGroup->map(function ($item) {
                        return [
                            'name' => $item->specification_name,
                            'value' => $item->specification_value,
                        ];
                    })->unique(),
                ];
            })->values();

            return response()->json(["status" => true, "message" => "plants", "data" => $plantList]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred!',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function plantDetails(Request $request)
    {
        try {
            // Fetch plant details
            $plant = DB::table('plant')
                ->leftJoin('manufacturer', 'plant.manufacturer_id', '=', 'manufacturer.id')
                ->select(
                    'plant.*',
                    'manufacturer.full_name as manufacturer_full_name',
                    'manufacturer.email as manufacturer_email',
                    'manufacturer.mobile as manufacturer_mobile',
                    'manufacturer.manufacturer_name'
                )
                ->where('plant.id', $request->plant_id)
                ->first();
            //dd($request->plant_id);
            // Check if plant exists
            if (!$plant) {
                return response()->json(['message' => 'Plant not found'], 404);
            }

            // Fetch sales managers
            $salesManagers = DB::table('plant_sales_manager')
                ->where('plant_id', $plant->id)
                ->select('name', 'phone', 'email', 'designation')
                ->get();

            // Fetch plant media
            $media = DB::table('plant_media')
                ->where('plant_id', $plant->id)
                ->pluck('image_url');

            // Fetch specifications
            $specifications = DB::table('specifications')
                ->where('id', $plant->specification)
                ->select('name', 'values')
                ->get();

            // Structure the response data
            $data = [
                'id' => $plant->id,
                'plant_name' => $plant->plant_name,
                'email' => $plant->email,
                'phone' => $plant->phone,
                'description' => $plant->description,
                'full_address' => $plant->full_address,
                'zipcode' => $plant->zipcode,
                'price_range' => $plant->from_price_range . "-" . $plant->to_price_range,
                'type' => $plant->type,
                'manufacturer_full_name' => $plant->manufacturer_full_name,
                'manufacturer_email' => $plant->manufacturer_email,
                'manufacturer_mobile' => $plant->manufacturer_mobile,
                'manufacturer_name' => $plant->manufacturer_name,
                'sales_managers' => $salesManagers,
                'media' => $media,
                'specifications' => $specifications
            ];

            return response()->json(["status" => true, "message" => "Plant details", "data" => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function manufacturerListing2(Request $request)
    {
        try {
            // Validate inputs
            $validator = Validator::make($request->all(), [
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'location' => 'nullable|string',
                'bed' => 'nullable|string',
                'bath' => 'nullable|string',
                'sq_ft' => 'nullable|string',
                'price_range' => 'nullable|string',
                'distance_range' => 'nullable|numeric',
                'type' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Mapbox API Key
            $apiKey = env('MAPBOX_ACESS_TOKEN');;

            // Get filter inputs
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $location = $request->input('location');
            $beds = $request->input('bed');
            $baths = $request->input('bath');
            $sq_ft = $request->input('sq_ft');
            $type = $request->input('type');
            $priceRange = $request->input('price_range');
            $distance_miles = DB::table('miles_settings')->where('id', 1)->first();
            $miles = isset($distance_miles->miles) ? $distance_miles->miles * 1609.34 : 482803;
            $distanceRange = $request->input('distance_range') ?? $miles;

            // Initialize Guzzle client
            $client = new Client();

            // Initialize query
            $query = DB::table('plant')
                ->leftJoin('plant_login', 'plant.manufacturer_id', '=', 'plant_login.id')
                ->leftJoin('plant_sales_manager', 'plant_sales_manager.plant_id', '=', 'plant.id')
                ->leftJoin('plant_media', 'plant_media.plant_id', '=', 'plant.id')
                ->leftJoin('specifications as baths_spec', function ($join) {
                    $join->on('baths_spec.manufacturer_id', '=', 'plant_login.id')
                        ->where('baths_spec.name', '=', 'Baths');
                })
                ->leftJoin('specifications as beds_spec', function ($join) {
                    $join->on('beds_spec.manufacturer_id', '=', 'plant_login.id')
                        ->where('beds_spec.name', '=', 'Beds');
                })
                ->leftJoin('specifications as sq_ft_spec', function ($join) {
                    $join->on('sq_ft_spec.manufacturer_id', '=', 'plant_login.id')
                        ->where('sq_ft_spec.name', '=', 'SQ Ft');
                })
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
                    'plant.from_price_range as plant_from_price_range',
                    'plant.to_price_range as plant_to_price_range',
                    'plant.latitude as plant_latitude',
                    'plant.longitude as plant_longitude',
                    'plant_media.image_url as plant_image_url'
                )
                ->where('plant.manufacturer_id', '!=', null)
                ->where('plant_login.status', 1)
                ->when($location, function ($query, $location) {
                    // Uncomment the following line if you want to filter by location
                    // return $query->where('plant.full_address', 'like', '%' . $location . '%');
                })
                ->when($type, function ($query, $type) {
                    return $query->where('plant.type', $type);
                })
                ->when($priceRange, function ($query, $priceRange) {
                    if (intval($priceRange) != 0) {
                        return $query->where('plant.from_price_range', '<=', intval($priceRange))->where('plant.to_price_range', '>=', intval($priceRange));
                    }
                })
                ->when($baths, function ($query, $baths) {
                    return $query->where('baths_spec.values', 'like', '%' . $baths . '%');
                })
                ->when($beds, function ($query, $beds) {
                    if ($beds == "4+") {
                        return $query->where('beds_spec.values', '>=', 4);
                    } else {
                        return $query->where('beds_spec.values', 'like', '%' . $beds . '%');
                    }
                })
                ->when($sq_ft, function ($query, $sq_ft) {
                    $sq_ft = explode('-', $sq_ft);
                    $min = intval($sq_ft[0]);
                    $max = intval($sq_ft[1]);

                    return $query->where('sq_ft_spec.values', '>=', $min)
                        ->where('sq_ft_spec.values', '<=', $max);
                });

            // Fetch manufacturers
            $manufacturers = $query->orderBy('plant_login.id', 'DESC')->get();

            if ($manufacturers->isEmpty()) {
                return response()->json([
                    'data' => [],
                    'message' => 'Manufacturers not found',
                    'status' => false,
                ], 200);
            }

            // Structure the response data
            $data = collect([]);

            // Iterate through manufacturers to calculate distance and structure data
            foreach ($manufacturers as $manufacturer) {
                $distance = null;
                if ($latitude && $longitude && $manufacturer->plant_latitude && $manufacturer->plant_longitude) {
                    // Make request to Mapbox Directions API
                    $response = $client->request('GET', 'https://api.mapbox.com/directions/v5/mapbox/driving/' . $longitude . ',' . $latitude . ';' . $manufacturer->plant_longitude . ',' . $manufacturer->plant_latitude, [
                        'query' => [
                            'access_token' => $apiKey,
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

                // Only include manufacturers within the specified distance range
                if ($dis !== null && $dis <= $distanceRange) {
                    // Sales manager information
                    $salesManager = [
                        'name' => $manufacturer->sales_manager_name,
                        'email' => $manufacturer->sales_manager_email,
                        'phone' => "+1" . $manufacturer->sales_manager_phone,
                        'designation' => $manufacturer->sales_manager_designation,
                        'image' => $manufacturer->sales_manager_image ? asset('upload/sales-manager-images/' . $manufacturer->sales_manager_image) : null,
                    ];

                    // Check if the manufacturer ID already exists in $data
                    $existing = $data->where('plant_id', $manufacturer->plant_id)->first();

                    if (!$existing) {
                        $plantType = $manufacturer->plant_type;
                        switch ($plantType) {
                            case 'sw':
                                $plantType = 'Single Wide';
                                break;
                            case 'dw':
                                $plantType = 'Double Wide';
                                break;
                            case 'sw_dw':
                                $plantType = 'Single Wide & Double Wide';
                                break;
                            default:
                                $plantType = null;
                                break;
                        }
                        $data->push([
                            'plant_id' => $manufacturer->plant_id,
                            'plant_name' => $manufacturer->plant_name,
                            'plant_phone' => $manufacturer->plant_phone,
                            'sales_manager' => $salesManager,
                            'plant_location' => $manufacturer->plant_location,
                            'plant_type' => $plantType,
                            'plant_price_range' => $manufacturer->plant_from_price_range . "-" . $manufacturer->plant_to_price_range,
                            'plant_image_url' => $manufacturer->plant_image_url ? asset('upload/manufacturer-image/' . $manufacturer->plant_image_url) : null,
                            'distance' => $distance . ' Miles', // Distance in miles
                            'distance_value' => $dis, // Distance value in meters for sorting
                        ]);
                    }
                }
            }

            // Sort $data by 'distance_value' (ascending)
            $sortedData = $data->sortBy('distance_value')->values();

            return response()->json([
                'data' => $sortedData,
                'message' => 'Manufacturers retrieved successfully',
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }




    public function manufacturerListing(Request $request)
    {
        try {
            // Validate inputs
            $validator = Validator::make($request->all(), [
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'location' => 'nullable|string',
                'bed' => 'nullable|string',
                'bath' => 'nullable|string',
                'sq_ft' => 'nullable|string',
                'price_range' => 'nullable|string',
                'distance_range' => 'nullable|numeric',
                'type' => 'nullable|string',
                'user_id'=> 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Mapbox API Key
            $apiKey = env('MAPBOX_ACESS_TOKEN');;

            // Get filter inputs
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $location = $request->input('location');
            $beds = $request->input('bed');
            $baths = $request->input('bath');
            $sq_ft = $request->input('sq_ft');
            $type = $request->input('type');
            $priceRange = $request->input('price_range');
            $distance_miles = DB::table('miles_settings')->where('id', 1)->first();
            $miles = isset($distance_miles->miles) ? $distance_miles->miles * 1609.34 : 482803;
            $distanceRange = $request->input('distance_range') ?? $miles;
            $user_id = $request->input('user_id');

            $isSaved = false;

             if ($user_id && $latitude && $longitude) {
                    $existingLocation = DB::table('locations')
                        ->where('user_id', $user_id)
                        ->where('latitude', $latitude)
                        ->where('longitude', $longitude)
                        ->first();

                    $isSaved = $existingLocation ? true : false;
                }

            // Initialize Guzzle client
            $client = new Client();

            // Get the state from the latitude and longitude using Mapbox Geocoding API
            $state = null;
            if ($latitude && $longitude) {
                $response = $client->request('GET', 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . $longitude . ',' . $latitude . '.json', [
                    'query' => [
                        'access_token' => $apiKey,
                        'types' => 'region',
                    ],
                ]);

                $body = json_decode($response->getBody(), true);
                if (isset($body['features'][0]['text'])) {
                    $state = $body['features'][0]['text'];
                }
            }

            // Initialize query
            $query = DB::table('plant')
                ->leftJoin('plant_login', 'plant.manufacturer_id', '=', 'plant_login.id')
                ->leftJoin('plant_sales_manager', 'plant_sales_manager.plant_id', '=', 'plant.id')
                ->leftJoin('plant_media', 'plant_media.plant_id', '=', 'plant.id')
                ->leftJoin('specifications as baths_spec', function ($join) {
                    $join->on('baths_spec.manufacturer_id', '=', 'plant_login.id')
                        ->where('baths_spec.name', '=', 'Baths');
                })
                ->leftJoin('specifications as beds_spec', function ($join) {
                    $join->on('beds_spec.manufacturer_id', '=', 'plant_login.id')
                        ->where('beds_spec.name', '=', 'Beds');
                })
                ->leftJoin('specifications as sq_ft_spec', function ($join) {
                    $join->on('sq_ft_spec.manufacturer_id', '=', 'plant_login.id')
                        ->where('sq_ft_spec.name', '=', 'SQ Ft');
                })
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
                    'plant.from_price_range as plant_from_price_range',
                    'plant.to_price_range as plant_to_price_range',
                    'plant.latitude as plant_latitude',
                    'plant.longitude as plant_longitude',
                    'plant_media.image_url as plant_image_url',
                    'plant.state as plant_state',
                    'plant.city as plant_city', // Ensure the state is being stored in the plants table
                    'plant_login.image as plant_login_image',
                    'plant.web_link',
                    'plant_login.business_name as business_name',
                )
                ->where('plant.manufacturer_id', '!=', null)
                ->where('plant_login.status', 1)
                ->when($state, function ($query, $state) {
                    return $query->where('plant.state', $state);
                })
                ->when($location, function ($query, $location) {
                    // Uncomment the following line if you want to filter by location
                    // return $query->where('plant.full_address', 'like', '%' . $location . '%');
                })
                ->when($type, function ($query, $type) {
                    return $query->where('plant.type', $type);
                })
                ->when($priceRange, function ($query, $priceRange) {
                    if (intval($priceRange) != 0) {
                        return $query->where('plant.from_price_range', '<=', intval($priceRange))->where('plant.to_price_range', '>=', intval($priceRange));
                    }
                })
                ->when($baths, function ($query, $baths) {
                    return $query->where('baths_spec.values', 'like', '%' . $baths . '%');
                })
                ->when($beds, function ($query, $beds) {
                    if ($beds == "4+") {
                        return $query->where('beds_spec.values', '>=', 4);
                    } else {
                        return $query->where('beds_spec.values', 'like', '%' . $beds . '%');
                    }
                })
                ->when($sq_ft, function ($query, $sq_ft) {
                    $sq_ft = explode('-', $sq_ft);
                    $min = intval($sq_ft[0]);
                    $max = intval($sq_ft[1]);

                    return $query->where('sq_ft_spec.values', '>=', $min)
                        ->where('sq_ft_spec.values', '<=', $max);
                });

            // Fetch manufacturers
            $manufacturers = $query->orderBy('plant_login.id', 'DESC')->get();

            if ($manufacturers->isEmpty()) {
                return response()->json([
                    'data' => [],
                    'user_id' => $user_id ?? '',
                    'is_saved' => $isSaved,
                    'message' => 'Manufacturers not found',
                    'status' => true,
                ], 200);
            }

            // Structure the response data
            $data = collect([]);

            // Iterate through manufacturers to calculate distance and structure data
            foreach ($manufacturers as $manufacturer) {
                $distance = null;
                $dis = null;
                if ($latitude && $longitude && $manufacturer->plant_latitude && $manufacturer->plant_longitude) {
                    // Make request to Mapbox Directions API
                    $response = $client->request('GET', 'https://api.mapbox.com/directions/v5/mapbox/driving/' . $longitude . ',' . $latitude . ';' . $manufacturer->plant_longitude . ',' . $manufacturer->plant_latitude, [
                        'query' => [
                            'access_token' => $apiKey,
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

                // Only include manufacturers within the specified distance range
                if ($dis !== null && $dis <= $distanceRange) {
                    // Sales manager information
                    $salesManager = [
                        'name' => $manufacturer->sales_manager_name,
                        'email' => $manufacturer->sales_manager_email,
                        'phone' => "+1" . $manufacturer->sales_manager_phone,
                        'designation' => $manufacturer->sales_manager_designation,
                        'image' => $manufacturer->sales_manager_image ? asset('upload/sales-manager-images/' . $manufacturer->sales_manager_image) : null,
                    ];

                    // Check if the manufacturer ID already exists in $data
                    $existing = $data->where('plant_id', $manufacturer->plant_id)->first();

                    if (!$existing) {
                        $plantType = $manufacturer->plant_type;
                        switch ($plantType) {
                            case 'sw':
                                $plantType = 'Single Wide';
                                break;
                            case 'dw':
                                $plantType = 'Double Wide';
                                break;
                            case 'sw_dw':
                                $plantType = 'Single Wide & Double Wide';
                                break;
                        }

                        $data->push([
                            'logo_image' => $manufacturer->plant_login_image ? asset('upload/manufacturer-image/' . $manufacturer->plant_login_image) : asset('images/defaultuser.png'),
                            'plant_id' => $manufacturer->plant_id,
                            'website' => $manufacturer->web_link,
                            'business_name'=> $manufacturer->business_name,
                            'plant_name' => $manufacturer->plant_name,
                            'plant_location' => $manufacturer->plant_location,
                            'plant_city_state' => $manufacturer->plant_city.','. $manufacturer->plant_state,
                            'plant_phone' => $manufacturer->plant_phone ? '+1' . $manufacturer->plant_phone : 'N/A',
                            'plant_image_url' => $manufacturer->plant_image_url ? asset('upload/manufacturer-image/' . $manufacturer->plant_image_url) : null,
                            'plant_type' => $plantType,
                            'plant_price_range' => '$' . $manufacturer->plant_from_price_range . ' - $' . $manufacturer->plant_to_price_range,
                            'distance_value' => $distance,
                            'distance' =>  round($distance) . ' Miles',
                            'sales_manager' => $salesManager, // Initialize with the first sales manager,
                            
                        ]);
                    } else {
                        // If the manufacturer ID already exists, append the sales manager
                        $existing['sales_manager'][] = $salesManager;
                    }
                }
            }

            // Sort the data by distance in ascending order
            $sortedData = $data->sortBy('distance_value');

            return response()->json([
                'user_id' => $user_id ?? '',
                'is_saved' => $isSaved,
                'data' => $sortedData->values()->toArray(), // Use values() to re-index the array
                'message' => 'Manufacturers found',
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    public function manufacturerListingg(Request $request)
    {
        try {
            // Validate inputs
            $validator = Validator::make($request->all(), [
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'location' => 'nullable|string',
                'bed' => 'nullable|string',
                'bath' => 'nullable|string',
                'sq_ft' => 'nullable|string',
                'price_range' => 'nullable|string',
                'distance_range' => 'nullable|numeric',
                'type' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Mapbox API Key
            $apiKey = env('GOOGLE_API_KEY');

            // Get filter inputs
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $location = $request->input('location');
            $beds = $request->input('bed');
            $baths = $request->input('bath');
            $sq_ft = $request->input('sq_ft');
            $type = $request->input('type');
            $priceRange = $request->input('price_range');
            $distance_miles = DB::table('miles_settings')->where('id', 1)->first();
            $miles = isset($distance_miles->miles) ? $distance_miles->miles * 1609.34 : 482803;
            $distanceRange = $request->input('distance_range') ?? $miles;
            $isSaved = false;
            // Initialize Guzzle client
            $client = new Client();

            // Get the state from the latitude and longitude using Mapbox Geocoding API
            $state = null;
            if ($latitude && $longitude) {
                $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
                    'query' => [
                        'latlng' => $latitude . ',' . $longitude,
                        'key' => $apiKey,
                    ],
                ]);

                $body = json_decode($response->getBody(), true);
                //dd($body);
                if (isset($body['results'][0]['address_components'])) {
                    foreach ($body['results'][0]['address_components'] as $component) {
                        if (in_array('administrative_area_level_1', $component['types'])) {
                            $state = $component['long_name'];
                            break;
                        }
                    }
                }
            }


            // Initialize query
            $query = DB::table('plant')
                ->leftJoin('plant_login', 'plant.manufacturer_id', '=', 'plant_login.id')
                ->leftJoin('plant_sales_manager', 'plant_sales_manager.plant_id', '=', 'plant.id')
                ->leftJoin('plant_media', 'plant_media.plant_id', '=', 'plant.id')
                ->leftJoin('specifications as baths_spec', function ($join) {
                    $join->on('baths_spec.manufacturer_id', '=', 'plant_login.id')
                        ->where('baths_spec.name', '=', 'Baths');
                })
                ->leftJoin('specifications as beds_spec', function ($join) {
                    $join->on('beds_spec.manufacturer_id', '=', 'plant_login.id')
                        ->where('beds_spec.name', '=', 'Beds');
                })
                ->leftJoin('specifications as sq_ft_spec', function ($join) {
                    $join->on('sq_ft_spec.manufacturer_id', '=', 'plant_login.id')
                        ->where('sq_ft_spec.name', '=', 'SQ Ft');
                })
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
                    'plant.from_price_range as plant_from_price_range',
                    'plant.to_price_range as plant_to_price_range',
                    'plant.latitude as plant_latitude',
                    'plant.longitude as plant_longitude',
                    'plant_media.image_url as plant_image_url',
                    'plant.state as plant_state', // Ensure the state is being stored in the plants table
                    'plant.city as plant_city',
                    'plant_login.image as plant_login_image',
                    'plant.web_link',
                    'plant_login.business_name as business_name',
                )
                ->where('plant.manufacturer_id', '!=', null)
                ->where('plant_login.status', 1)
                ->when($state, function ($query, $state) {
                    return $query->where('plant.state', $state);
                })
                ->when($location, function ($query, $location) {
                    // Uncomment the following line if you want to filter by location
                    // return $query->where('plant.full_address', 'like', '%' . $location . '%');
                })
                ->when($type, function ($query, $type) {
                    return $query->where('plant.type', $type);
                })
                ->when($priceRange, function ($query, $priceRange) {
                    if (intval($priceRange) != 0) {
                        return $query->where('plant.to_price_range', '<=', intval($priceRange));
                    }
                })
                ->when($baths, function ($query, $baths) {

                    if ($baths == "4+") {
                        return $query->whereRaw('CAST(baths_spec.values AS UNSIGNED) >= ?', [4]);
                    } else {
                        return $query->where('baths_spec.values', 'like', '%' . $baths . '%');
                    }
                })
                ->when($beds, function ($query, $beds) {
                    if ($beds == "5+") {

                        return $query->whereRaw('CAST(beds_spec.values AS UNSIGNED) >= ?', [5]);
                    } else {
                        return $query->where('beds_spec.values', 'like', '%' . $beds . '%');
                    }
                })
                ->when($sq_ft, function ($query, $sq_ft) {
                    $sq_ft = explode('-', $sq_ft);
                    $min = intval($sq_ft[0]);
                    $max = intval($sq_ft[1]);

                    return $query->where('sq_ft_spec.values', '>=', $min)
                        ->where('sq_ft_spec.values', '<=', $max);
                });

            // Fetch manufacturers
            $manufacturers = $query->orderBy('plant_login.id', 'DESC')->get();

            if ($manufacturers->isEmpty()) {
                return response()->json([
                    'data' => [],
                    'user_id' => $user_id ?? '',
                    'is_saved' => $isSaved,
                    'message' => 'Manufacturers not found',
                    'status' => true,
                ], 200);
            }

            // Structure the response data
            $data = collect([]);

            // Iterate through manufacturers to calculate distance and structure data
            foreach ($manufacturers as $manufacturer) {
                $distance = null;
                $dis = null;
                if ($latitude && $longitude && $manufacturer->plant_latitude && $manufacturer->plant_longitude) {
                    // Make request to Mapbox Directions API
                    $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/distancematrix/json', [
                        'query' => [
                            'origins' => $latitude . ',' . $longitude,
                            'destinations' => $manufacturer->plant_latitude . ',' . $manufacturer->plant_longitude,
                            'key' => $apiKey,
                            'mode' => 'driving',
                        ],
                    ]);

                    $body = json_decode($response->getBody(), true);

                    // Extract distance from response
                    if (isset($body['rows'][0]['elements'][0]['distance']['value'])) {
                        $dis = $body['rows'][0]['elements'][0]['distance']['value']; // Distance in meters
                        $distance = round($dis * 0.000621371, 2); // Convert meters to miles
                    }
                }

                // Only include manufacturers within the specified distance range
                if ($dis !== null && $dis <= $distanceRange) {
                    // Sales manager information
                    $salesManager = [
                        'name' => $manufacturer->sales_manager_name,
                        'email' => $manufacturer->sales_manager_email,
                        'phone' => "+1" . $manufacturer->sales_manager_phone,
                        'designation' => $manufacturer->sales_manager_designation,
                        'image' => $manufacturer->sales_manager_image ? asset('upload/sales-manager-images/' . $manufacturer->sales_manager_image) : null,
                    ];

                    // Check if the manufacturer ID already exists in $data
                    $existing = $data->where('plant_id', $manufacturer->plant_id)->first();

                    if (!$existing) {
                        $plantType = $manufacturer->plant_type;
                        switch ($plantType) {
                            case 'sw':
                                $plantType = 'Single Wide';
                                break;
                            case 'dw':
                                $plantType = 'Double Wide';
                                break;
                            case 'sw_dw':
                                $plantType = 'Single Wide & Double Wide';
                                break;
                        }

                        $data->push([
                            'logo_image' => $manufacturer->plant_login_image ? asset('upload/manufacturer-image/' . $manufacturer->plant_login_image) : asset('images/defaultuser.png'),
                            'plant_id' => $manufacturer->plant_id,
                            'website' => $manufacturer->web_link,
                            'business_name'=> $manufacturer->business_name,
                            'plant_name' => $manufacturer->plant_name,
                            'plant_location' => $manufacturer->plant_location,
                            'plant_city_state' => $manufacturer->plant_city.','. $manufacturer->plant_state,
                            'plant_phone' => $manufacturer->plant_phone ? '+1' . $manufacturer->plant_phone : 'N/A',
                            'plant_image_url' => $manufacturer->plant_image_url ? asset('upload/manufacturer-image/' . $manufacturer->plant_image_url) : null,
                            'plant_type' => $plantType,
                            'plant_price_range' => '$' . $manufacturer->plant_from_price_range . ' - $' . $manufacturer->plant_to_price_range,
                            'distance_value' => $distance,
                            'distance' =>  round($distance) . ' Miles',
                            'sales_manager' => $salesManager, // Initialize with the first sales manager
                        ]);
                    } else {
                        // If the manufacturer ID already exists, append the sales manager
                        $existing['sales_manager'][] = $salesManager;
                    }
                }
            }

            // Sort the data by distance in ascending order
            $sortedData = $data->sortBy('distance_value');

            return response()->json([
                'user_id' => $user_id ?? '',
                'is_saved' => $isSaved,
                'data' => $sortedData->values()->toArray(), // Use values() to re-index the array
                'message' => 'Manufacturers found',
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }








    public function getManufacturerDetails(Request $request)
    {
        try {
            $id = $request->id;
            $plant_id = $request->plant_id;
            $distance = $request->input('distance');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $apiKey = env('MAPBOX_ACESS_TOKEN');;
            // Fetch the specified plant for the manufacturer
            $plants = DB::table('plant')
                ->where('id', $plant_id) // Filter by plant_id
                ->select(
                    'id as plant_id',
                    'plant_name',
                    'description as plant_description',
                    'full_address',
                    'zipcode',
                    'price_range',
                    'from_price_range',
                    'to_price_range',
                    'price_range',
                    'type',
                    'phone',
                    'shipping_cost',
                    'web_link',
                    'latitude',
                    'longitude',
                    'specification',
                    'manufacturer_id',
                    'state',
                    'city',
                )
                ->get();
                if (!$plants->isEmpty()) {
                    $firstPlant = $plants->first();
                    $plant_login = DB::table('plant_login')
                        ->where('id', $firstPlant->manufacturer_id)
                        ->first();
                }
            // Check if plant exists
            if ($plants->isEmpty()) {
                return response()->json([
                    'message' => 'Plant not found',
                    'status' => false,
                ], 200);
            }

            // Initialize Guzzle client for Mapbox API
            $client = new Client();
           // dd($distance);
            // Initialize empty data array
            $data = [];

            // Iterate through each plant to calculate distance and structure data
            foreach ($plants as $plantItem) {
                //$distance = null;
                // if ($latitude && $longitude && $plantItem->latitude && $plantItem->longitude) {
                //     // Make request to Mapbox Directions API
                //     $response = $client->request('GET', 'https://api.mapbox.com/directions/v5/mapbox/driving/' . $longitude . ',' . $latitude . ';' . $plantItem->longitude . ',' . $plantItem->latitude, [
                //         'query' => [
                //             'access_token' => $apiKey,
                //             'geometries' => 'geojson',
                //             'overview' => 'simplified',
                //         ],
                //     ]);

                //     $body = json_decode($response->getBody(), true);

                //     // Extract distance from response
                //     if (isset($body['routes'][0]['distance'])) {
                //         $dis = $body['routes'][0]['distance']; // Distance in meters
                //         $distance = round($dis * 0.000621371, 2); // Convert meters to miles
                //     }
                // }

                // Fetch sales managers for the specified plant
                $salesManagers = DB::table('plant_sales_manager')
                    ->where('plant_id', $plant_id) // Filter by plant_id
                    ->select('plant_id', 'name', 'email', 'phone', 'designation', 'image')
                    ->get();

                // Fetch plant images
                $plantImages = DB::table('plant_media')
                    ->where('plant_id', $plant_id)
                    ->select('id', 'image_url')
                    ->get();

                $plantImagesWithAssets = $plantImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'media_url' => !empty($image->image_url) ? asset('upload/manufacturer-image/' . $image->image_url) : null,
                    ];
                });

                // Explode specification IDs into an array
                $specificationIds = explode(',', $plantItem->specification);

                // Fetch specifications based on IDs
                $specifications = DB::table('specifications')
                    ->whereIn('id', $specificationIds)
                    ->get();

                // Convert type to readable format
                $typeReadable = match ($plantItem->type) {
                    'sw' => 'Single Wide',
                    'dw' => 'Double Wide',
                    'sw_dw' => 'Single Wide & Double Wide',
                    default => null,
                };

                // Fetch shipping cost based on plant type
                $shippingCostRecord = ShippingCost::where('type', $plantItem->type)->first();
                $shippingCost = $shippingCostRecord ? $shippingCostRecord->shipping_cost : 0;
                $totalShippingCost = $shippingCost * $distance;

                // Prepare plant data with specifications array
                $plantData = [
                    'plant_id' => $plantItem->plant_id,
                    'website' => $plantItem->web_link,
                    'plant_name' => $plantItem->plant_name,
                    'plant_phone' => $plantItem->phone ? '+1' . $plantItem->phone : null,
                    'plant_description' => $plantItem->plant_description,
                    'full_address' => $plantItem->full_address,
                    'city_state' => $plantItem->city.','.$plantItem->state,
                    'zipcode' => $plantItem->zipcode,
                    'price_range' => $plantItem->from_price_range . "-" . $plantItem->to_price_range,
                    'shipping_cost_per_mile' => '$' . $shippingCost,
                    'total_shipping_charges' => '$' . number_format($totalShippingCost, 2),
                    'type' => $typeReadable,
                    'latitude' => $plantItem->latitude,
                    'longitude' => $plantItem->longitude,
                    'distance' => round($distance) > 0 ? round($distance) . ' miles' : '0 miles',
                    'plant_images' => $plantImagesWithAssets,
                    'images_count' => count($plantImagesWithAssets),
                    'sales_managers' => [], // Initialize sales managers array
                    'specifications' => $specifications,
                    'about_the_company' => $plant_login->about ?? '',
                    'logo_image' => $plant_login->image ? asset('upload/manufacturer-image/' . $plant_login->image) : asset('images/defaultuser.png'),
                    'business_name' => $plant_login->business_name ?? 'N/A',
                ];

                // Add each sales manager to the plant data
                foreach ($salesManagers as $salesManager) {
                    $plantData['sales_managers'][] = [
                        'name' => $salesManager->name,
                        'email' => $salesManager->email,
                        'phone' => '+1' . $salesManager->phone,
                        'designation' => $salesManager->designation,
                        'image' => $salesManager->image ? asset('upload/sales-manager-images/' . $salesManager->image) : null,
                    ];
                }

                $data = $plantData;
            }

            return response()->json([
                'data' => $data,
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the manufacturer details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function filter_static_data()
    {


        $data = [];
        $beds = [
            [
                'title' => '01',
                'value' => '1'
            ],
            [
                'title' => '02',
                'value' => '2'
            ],
            [
                'title' => '03',
                'value' => '3'
            ],
            [
                'title' => '04',
                'value' => '4'
            ],
            [
                'title' => '05+',
                'value' => '5+'
            ],
        ];
        $baths = [
            [
                'title' => '01',
                'value' => '1'
            ],
            [
                'title' => '02',
                'value' => '2'
            ],
            [
                'title' => '03',
                'value' => '3'
            ],
            [
                'title' => '04+',
                'value' => '4+'
            ],

        ];
        $sq_ft = [
            [
                'title' => '100-500',
                'value' => '100-500'
            ],
            [
                'title' => '500-1000',
                'value' => '500-1000'
            ],
            [
                'title' => '1000-2000',
                'value' => '1000-2000'
            ],
            [
                'title' => '2500+',
                'value' => '2500-10000'
            ],

        ];
        $distance_range = [
            [
                'title' => '100',
                'value' => 100
            ],
            [
                'title' => '200',
                'value' => 200
            ],
            [
                'title' => '300',
                'value' => 300
            ]

        ];

        $types = [
            [
                'title' => 'Single Wide',
                'value' => "sw"
            ],
            [
                'title' => 'Double Wide',
                'value' => 'dw'
            ],
            [
                'title' => 'Both',
                'value' => 'sw_dw'
            ]

        ];
        $price_range = [
            [
                'from' => 0,
                'to' => 10000
            ],


        ];
        $data = [
            'beds' => $beds,
            'sq_ft' => $sq_ft,
            'distance_range' => $distance_range,
            'types' => $types,
            'baths' => $baths,
            'price_range' => $price_range,

        ];
        return response()->json([
            'data' => $data,
            'status' => true,
        ], 200);
    }



    public function DropdownValues()
    {


        $data = [];
        $communities_sales_lot = [
            [
                'label' => '1-10',
                'value' => '1-10'
            ],
            [
                'label' => '10-25',
                'value' => '10-25'
            ],
            [
                'label' => '25-50',
                'value' => '25-50'
            ],
            [
                'label' => '50-100',
                'value' => '50-100'
            ],
            [
                'label' => '100+',
                'value' => '100+'
            ],
        ];
        $buy_mhs = [
            [
                'label' => '1-3',
                'value' => '1-3'
            ],
            [
                'label' => '3-5',
                'value' => '3-5'
            ],
            [
                'label' => '5-10',
                'value' => '3'
            ],
            [
                'label' => '10-25',
                'value' => '10-25'
            ],
            [
                'label' => '25+',
                'value' => '25+'
            ]

        ];
        $data = [
            'communities_sales_lot' => $communities_sales_lot,
            'buy_mhs' => $buy_mhs,

        ];
        return response()->json([
            'data' => $data,
            'status' => true,
        ], 200);
    }





    public function forgetpassword(Request $request) 
    {
        try {
            // Validation of the email field
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            // Return error if validation fails
            if ($validator->fails()) {
                return errorMsg($validator->errors()->first());
            }

            $email = $request->email;

            // Check if the user exists with the given email and status 1
            $user = User::where('email', $email)->first();
            // dd($user);

            if (!$user) {
                // User does not exist, return an error response
                return response()->json([
                    'status' => false,
                    'message' => 'This email is not registered with Show Search',
                ]);
            }

            if ($user->status == 0) {
                // User exists but the account is deactivated by admin
                return response()->json([
                    'status' => false,
                    'message' => 'Your account has been deactivated by the admin.',
                ]);
            }elseif($user->status == 2){
                return response()->json([
                    'status' => false,
                    'message' => 'This email is not registered with Show Search',
                ]);
            }

            // Check if OTP already exists for this email
            $exist = Otp::where('email', $email)->first();
            $code = rand(1000, 9999); // Generate a four-digit OTP code

            if ($exist) {
                // Update the existing OTP
                $exist->update(['otp' => $code]);
            } else {
                // Create a new OTP entry
                Otp::create([
                    'email' => $email,
                    'otp' => $code,
                    'user_id' => $user->id,
                ]);
            }

            // Prepare data for the response and email
            $data = [
                'status' => true,
                'message' => 'Verification code has been sent',
                'code' => $code,
                'email' => $user->email,
            ];

            $mailData = [
                'body' => 'You have requested to change your password. Please find the below details to do the same.',
                'code' => 'Your One Time Password to change your password is ' . $code,
                'email' => $email,
                'name' => $user->fullname,
            ];

            $subject = 'Reset Your Password';

            // Attempt to send the email
            try {
                Mail::to($email)->send(new SendOTPMail($mailData, $subject));
            } catch (\Exception $mailException) {
            }

            // Return a success response with the OTP data
            return response()->json($data);

        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }



    public function verifyotp(Request $request) 
    {
        try {
            $data=array();
            $otp = $request->otp;
            $validator = Validator::make($request->all() , [
                'email' => 'required|email',
                'otp' => 'required|digits:4|numeric',
            ]);
            if ($validator->fails())
            {
                return errorMsg($validator->errors()->first());
            }
        
            $user = Otp::where('email',$request->email)->orderBy('id','DESC')->first();
            if(!empty($user))
            {
                $user_detail = User::where('email',$request->email)->where('status',1)->orderBy('id','DESC')->first();
                if(!empty($user_detail))
                {
                    $remember_token = $user_detail->remember_token;
                }else{
                    $remember_token = '';
                }
                if($user->otp == $otp)
                {
                    $validTill = strtotime($user->updated_at) + (24*60*60);
                    if (strtotime("now") > $validTill) {
                        $data['status']=true;
                        $data['message'] = "OTP has expired.";
                        $data['user_id'] = '';
                        $data['token'] = '';
                        return response()->json($data);
                    }else{
                        $data['status']=true;
                        $data['message']="OTP verified";
                        $data['user_id'] = $user->user_id;
                        $data['token'] = $remember_token;
                        return response()->json($data);
                    }
                }else{
                    $data['status']=false;
                    $data['message']="Please enter valid OTP";
                    $data['user_id'] = '';
                    $data['token'] = '';
                    return response()->json($data);
                }
            }else{
                $data['status'] = false;
                $data['message'] = 'Otp does not exits';
                return response()->json($data);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }


    public function deleteUserAccount(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // If the user is not found (which shouldn't happen since they're authenticated), return an error
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }

            // Delete the user account
            $user->status = 2;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User account deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting the account',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function deleteAccount(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // If the user is not found (which shouldn't happen since they're authenticated), return an error
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 200);
            }

            // Delete the user account
            $user->status = 2;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User account deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting the account',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getPolicies(Request $request)
    {
        // URLs for the Privacy Policy and Terms and Conditions
        $privacyPolicyUrl = 'https://showsearch.net/pages/privacy_policy';  // Or the actual URL if hosted elsewhere
        $termsAndConditionsUrl = 'https://showsearch.net/pages/terms-and-conditions';  // Or the actual URL if hosted elsewhere

        // Return the response with the URLs
        return response()->json([
            'status' => true,
            'message' => 'Policies fetched successfully',
            'data' => [
                'privacy_policy_url' => $privacyPolicyUrl,
                'terms_and_conditions_url' => $termsAndConditionsUrl,
            ]
        ], 200);
    }
}
