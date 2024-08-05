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
use App\Models\Specifications;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

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
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password|same:password',
            'status' => 'nullable|string|max:10',
            'device_token' => 'nullable|string',
            'type' => 'nullable|integer|in:1,2',
        ], [
            'email.unique' => 'This email is already registered with Show Search.',
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
            'location' => 1,
            'mailverified' => true,
            'password' => Hash::make($request->password),
            'status' => $request->status ?? '1',
            'device_token' => $request->device_token,
            'type' => $request->type ?? 2,
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

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials', 'status' => 0], 200);
        }

        // Create a new personal access token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the token and user information
        return response()->json([
            'message' => 'User successfully logged in',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'status' => 1,
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
            // Create an array of objects format for user details
            $userDetails =
                (object) [
                    'id' => $user->id,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'business_name' => $user->business_name,
                    'business_address' => $user->business_address,
                    'business_city' => $user->business_city,
                    'business_state' => $user->business_state,
                    'business_zipcode' => $user->business_zipcode,
                    'community_owner' => $user->community_owner,
                    'location' => $user->location,
                    'mailverified' => $user->mailverified,
                    'status' => $user->status,
                    'device_token' => $user->device_token,
                    'type' => $user->type,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            return response()->json([
                'user' => $userDetails,
                'contacted_manufacturers' => $processedManufacturers,
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
                'location' => 'nullable'
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
                'location' => 'nullable|integer|in:1,2,3,4',
                'status' => 'nullable|string|max:10',
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
            $user->fill($request->except(['password', 'device_token', 'type', 'mailverified', 'email']));
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
                return ($validator->errors()->first());
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
                $data['message'] = 'Something went wrong!';
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
        try {
            $locations = DB::table('locations')->get();


            $success['locations'] = $locations;
            return response()->json(["status" => true, "message" => "Locations", "data" => $success]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred!',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function LocationDetails(Request $request)
    {
        try {
            $locations = DB::table('locations')->where('id', $request->id)->first();


            $success['locations'] = $locations;
            return response()->json(["status" => true, "message" => "Locations", "data" => $success]);
        } catch (\Exception $e) {
            return response()->json([
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
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Google Maps API Key
            $apiKey = 'AIzaSyDtg_iY8FedOwjt419T7zaT0fHTcTYcwPE';

            // Get filter inputs
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $location = $request->input('location');
            $beds = $request->input('bed');
            $baths = $request->input('bath');
            $sq_ft = $request->input('sq_ft');
            $type = $request->input('type');
            $priceRange = $request->input('price_range');
            $distanceRange = $request->input('distance_range') ?? 482803;

            // Initialize Guzzle client
            $client = new Client();

            // Initialize query
            // $manufacturersQuery = DB::table('plant_login')
            //     ->leftJoin('plant', 'plant.manufacturer_id', '=', 'plant_login.id')
            //     ->leftJoin('plant_sales_manager', 'plant_sales_manager.plant_id', '=', 'plant.id')
            //     ->leftJoin('plant_media', 'plant_media.plant_id', '=', 'plant.id')
            //     ->leftJoin('specifications', 'specifications.manufacturer_id', '=', 'plant_login.id')
            //     ->select(
            //         'plant.id as plant_id',
            //         'plant.plant_name as plant_name',
            //         'plant.phone as plant_phone',
            //         'plant_sales_manager.name as sales_manager_name',
            //         'plant_sales_manager.email as sales_manager_email',
            //         'plant_sales_manager.designation as sales_manager_designation',
            //         'plant_sales_manager.phone as sales_manager_phone',
            //         'plant_sales_manager.image as sales_manager_image',
            //         'plant.full_address as plant_location',
            //         'plant.type as plant_type',
            //         'plant.price_range as plant_price_range',
            //         'plant.from_price_range as plant_from_price_range',
            //         'plant.to_price_range as plant_to_price_range',
            //         'plant.latitude as plant_latitude',
            //         'plant.longitude as plant_longitude',
            //         'plant_media.image_url as plant_image_url'
            //     )
            //     ->where('plant.manufacturer_id', '!=', null)
            //     ->where('plant_login.status', 1)
            //     ->when($location, function ($query, $location) {
            //         // return $query->where('plant.full_address', 'like', '%' . $location . '%');
            //     })
            //     ->when($type, function ($query, $type) {
            //         return $query->where('plant.type', $type);
            //     })
            //     ->when($priceRange, function ($query, $priceRange) {
            //         // return $query->where('plant.from_price_range', "<=", intval($priceRange))->where('plant.to_price_range', ">=", intval($priceRange));
            //         if (intval($priceRange) != 0) {
            //             return $query->where('plant.price_range', "<=", intval($priceRange));
            //         }
            //     })
            //     ->when($baths, function ($query, $baths) {


            //         return $query->where("specifications.name", "Baths")->where('specifications.values', 'like', '%' . $baths . '%');
            //     })
            //     ->when($beds, function ($query, $beds) {
            //         return $query->where("specifications.name", "Beds")->where('specifications.values', 'like', '%' . $beds . '%');
            //     })
            //     ->when($sq_ft, function ($query, $sq_ft) {

            //         $sq_ft = explode("-", $sq_ft);
            //         $min = intval($sq_ft[0]);
            //         $max = intval($sq_ft[1]);

            //         return $query->where("specifications.name", "SQ Ft")->where('specifications.values', '>=', $min)->where('specifications.values', '<=', $max);
            //     });
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

                    // Extract distance from response
                    if (isset($body['rows'][0]['elements'][0]['distance']['text'])) {
                        $distanceText = $body['rows'][0]['elements'][0]['distance']['text'];
                        $dis = $body['rows'][0]['elements'][0]['distance']['value'];

                        if (strpos($distanceText, 'mi') !== false) {
                            $distance = floatval(str_replace(' mi', '', $distanceText));
                            $distanceText = str_replace(' mi', ' Miles', $distanceText); // Replace ' mi' with ' Miles'
                            if ($distance < 1) {
                                $distanceText = '0 Miles';
                            }
                        } else {
                            // Convert km to miles if necessary
                            $distanceValue = floatval(str_replace(' km', '', $distanceText));
                            $distance = round($distanceValue * 0.621371, 2);
                            $distanceText = $distance . ' Miles';
                            if ($distance < 1) {
                                $distanceText = '0 Miles';
                            }
                        }
                    }
                }


                // Only include manufacturers within 50 miles
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
                            'distance' => $distanceText, // Distance in miles
                            'distance_value' => $dis, // Distance value in meters for sorting
                        ]);
                    }
                }
            }

            // Sort $data by 'distance_value' (ascending)
            $sortedData = $data->sortBy('distance_value')->values();

            return response()->json([
                'data' => $sortedData,
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the manufacturer details',
                'error' => $e->getMessage()
            ], 500);
        }
    }








    public function getManufacturerDetails(Request $request)
    {
        try {
            $id = $request->id;
            $plant_id = $request->plant_id;
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

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
                    'type',
                    'phone',
                    'shipping_cost',
                    'latitude',
                    'longitude',
                    'specification'
                )
                ->get();

            // Check if plant exists
            if ($plants->isEmpty()) {
                return response()->json([
                    'message' => 'Plant not found',
                    'status' => false,
                ], 200);
            }

            // Initialize Guzzle client for Google Maps API
            $client = new Client();

            // Initialize empty data array
            $data = [];

            // Iterate through each plant to calculate distance and structure data
            foreach ($plants as $plantItem) {
                $distance = null;
                if ($latitude && $longitude && $plantItem->latitude && $plantItem->longitude) {
                    // Make request to Google Maps Distance Matrix API
                    $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/distancematrix/json', [
                        'query' => [
                            'key' => 'AIzaSyDtg_iY8FedOwjt419T7zaT0fHTcTYcwPE',
                            'origins' => $latitude . ',' . $longitude,
                            'destinations' => $plantItem->latitude . ',' . $plantItem->longitude,
                            'units' => 'imperial', // 'imperial' for miles
                        ],
                    ]);

                    $body = json_decode($response->getBody(), true);

                    // Extract distance from response
                    if (isset($body['rows'][0]['elements'][0]['distance']['text'])) {
                        $distanceText = $body['rows'][0]['elements'][0]['distance']['text'];
                        if (strpos($distanceText, 'mi') !== false) {
                            $distance = floatval(str_replace(' mi', '', $distanceText));
                        } else {
                            // Convert km to miles if necessary
                            $distanceValue = floatval(str_replace(' km', '', $distanceText));
                            $distance = round($distanceValue * 0.621371, 2);
                        }
                    }
                }

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
                    'plant_name' => $plantItem->plant_name,
                    'plant_phone' => $plantItem->phone ? '+1' . $plantItem->phone : null,
                    'plant_description' => $plantItem->plant_description,
                    'full_address' => $plantItem->full_address,
                    'zipcode' => $plantItem->zipcode,
                    'price_range' => $plantItem->from_price_range . "-" . $plantItem->to_price_range,
                    'shipping_cost_per_mile' => '$' . $shippingCost,
                    'total_shipping_charges' => '$' . number_format($totalShippingCost, 2),
                    'type' => $typeReadable,
                    'latitude' => $plantItem->latitude,
                    'longitude' => $plantItem->longitude,
                    'distance' => $distance . ' Miles',
                    'plant_images' => $plantImagesWithAssets,
                    'images_count' => count($plantImagesWithAssets),
                    'sales_managers' => [], // Initialize sales managers array
                    'specifications' => $specifications,
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
            ],  [
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
            ], [
                'title' => '1000-2000',
                'value' => '1000-2000'
            ], [
                'title' => '2500+',
                'value' => '2500-10000'
            ],

        ];
        $distance_range = [
            [
                'title' => '100',
                'value' => 160934
            ],
            [
                'title' => '200',
                'value' => 321869
            ], [
                'title' => '300',
                'value' => 482803
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
            ], [
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
}
