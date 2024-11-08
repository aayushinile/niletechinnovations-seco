<?php

namespace App\Http\Controllers;

use App\Exports\CorporatePlantsExport;
use App\Models\Manufacturer;
use App\Models\ManufacturerAttributes;
use App\Models\PlantSalesManager;
use App\Models\Plant;
use App\Models\PlantLogin;
use App\Models\Specifications;
use App\Models\PlantMedia;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Exception;
use Carbon\Carbon;
use Mail;
use App\Mail\ForgetPassword;
use App\Mail\ApprovalMail;
use GuzzleHttp\Client;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ManufacturerEnquiriesExport;
use App\Imports\PlantImport;
use App\Imports\PlantManufacturerImport;
use Illuminate\Support\Facades\Log;

class ManufacturerController extends Controller
{
    public function loginManufacturer()
    {
        if (auth()->guard('manufacturer')->check()) {
            if (request()->has('register')) {
                auth()->guard('manufacturer')->logout();
                auth()->guard("web")->logout();
            } else {
                return redirect()->route('manufacturer.dashboard');
            }
        }
        return view('manufacturer.login');
    }

    public function authenticate(Request $request)
    {
        //dd($request->all());
        $credentials = $request->only('email', 'password');

        if (auth()->guard('manufacturer')->attempt($credentials)) {
            // Authentication passed
            return redirect()->route('manufacturer.dashboard');
        }

        // Authentication failed
        return redirect()->back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }


    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Fetch new enquiries
        // $userSettings = DB::table('settings')->where('manufacturer_id', $user->id)->first();
        // $selectedCountry = $userSettings->country ?? 'United States'; 
        $new_enquiries = DB::table('contact_manufacturer')
        ->leftJoin('plant', 'contact_manufacturer.plant_id', '=', 'plant.id')
        ->leftJoin('plant_login','plant_login.id','=','plant.manufacturer_id')
        ->where('plant_login.id', $user->id)
        ->where('contact_manufacturer.status',0)
        ->leftJoin('users', 'contact_manufacturer.user_id', '=', 'users.id')
        ->orderBy('contact_manufacturer.id', 'DESC')
        ->select(
            'contact_manufacturer.id as enquiry_id',
            'contact_manufacturer.user_name as enquiry_name',
            'contact_manufacturer.phone_no as enquiry_phone',
            'contact_manufacturer.email as enquiry_mail',
            'contact_manufacturer.company_name as company_name',
            'contact_manufacturer.type as type',
            'users.id as user_id',
            'contact_manufacturer.message',
            'contact_manufacturer.status',
            'contact_manufacturer.created_at',
            'users.fullname',
            'users.email',
            'users.mobile',
            'users.business_name',
        )
        ->paginate(10);

        // Count plants
        $count = DB::table('plant')->where('manufacturer_id', $user->id)->count();
        $plants = DB::table('plant')->where('manufacturer_id', $user->id)->take(3)->get();

        // Fetch country flags from config
        $countryFlags = config('country_flags');
        //dd($countryFlags);
        // Fetch city and state for each plant using Mapbox API
        $locationCounts = [];
        foreach ($plants as $plant) {
            $response = Http::get('https://api.mapbox.com/geocoding/v5/mapbox.places/' . $plant->longitude . ',' . $plant->latitude . '.json', [
                'access_token' => 'pk.eyJ1IjoidXNlcnMxIiwiYSI6ImNsdGgxdnpsajAwYWcya25yamlvMHBkcGEifQ.qUy8qSuM_7LYMSgWQk215w',
                'types' => 'place',
                'limit' => 1
            ]);

            $data = $response->json();
            //dd($data);

            // Example assuming first result is city and state
            if (isset($data['features'][0]['place_name'])) {
                $placeName = $data['features'][0]['place_name'];
                $placeParts = explode(', ', $placeName);

                $city = isset($placeParts[0]) ? trim($placeParts[0]) : 'Unknown';
                $state = isset($placeParts[1]) ? trim($placeParts[1]) : 'Unknown';
                //dd($state);
                $plant->city = $city;
                $plant->state = $state;
                //dd($country);
                // if ($country && isset($countryFlags[$country])) {
                //     $countrySymbol = $countryFlags[$country];
                // } else {
                //     $countrySymbol = '🏳️'; // Default flag if country not found
                // }

                $locationKey = $city . ', ' . $state;

                if (!isset($locationCounts[$locationKey])) {
                    $locationCounts[$locationKey] = [
                        'count' => 0,
                        'city' => $city,
                        'state' => $state
                    ];
                }

                $locationCounts[$locationKey]['count']++;
            } else {
                $plant->city = 'Unknown';
                $plant->state = 'Unknown';
            }
        //dd($locationCounts);
        }

        return view('manufacturer.dashboard', compact('new_enquiries', 'user', 'plants', 'count', 'locationCounts'));
    }



    public function ManufacturerSignup(Request $request)
    {
        return view('manufacturer.signup');
    }

    public function ManufacturerCorpSignup(Request $request)
    {
        return view('manufacturer.corp_signup');
    }



    public function saveManufacturer(Request $request)
{
    try {
        // Define validation rules
        $rules = [
            'full_name' => 'nullable|string|max:255',
            'manufacturer_full_name' => 'nullable|string|max:255',
            'mobile' => 'nullable',
            'email' => 'required|email|max:255|unique:plant_login,email',
            'manufacturer_name' => 'nullable|string|max:255',
            'manufacturer_address' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'latitude' => 'required_with:full_address|numeric',
            'longitude' => 'required_with:full_address|numeric',
            'rep_type' => 'nullable',
        ];
        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        

        // Get the full address
        $fullAddress = $request->input('manufacturer_address');

        // Initialize the HTTP client
        $client = new Client();

        // Make the request to the Mapbox Geocoding API
        $response = $client->get('https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($fullAddress) . '.json', [
            'query' => [
                'access_token' => 'pk.eyJ1IjoidXNlcnMxIiwiYSI6ImNsdGgxdnpsajAwYWcya25yamlvMHBkcGEifQ.qUy8qSuM_7LYMSgWQk215w',
            ],
        ]);

        // Parse the response
        $data = json_decode($response->getBody(), true);
        $features = $data['features'][0];

        // Extract city, state, and country from the response
        $city = $features['text'] ?? ''; // Use the main text as city if context doesn't have it
        $state = '';
        $country = '';

        foreach ($features['context'] as $context) {
            if (strpos($context['id'], 'place') === 0) {
                $city = $context['text'];
            } elseif (strpos($context['id'], 'region') === 0) {
                $state = $context['text'];
            } elseif (strpos($context['id'], 'country') === 0) {
                $country = $context['text'];
            }
        }

        // Log the API response for debugging
        // Log::info('Mapbox API response', ['data' => $data]);

        // Create new manufacturer
        $plant = new PlantLogin();
        $plant->plant_name = $request->input('full_name');
        $plant->phone = $request->input('mobile');
        $plant->email = $request->input('email');
        $plant->full_address = $fullAddress;
        $plant->password = bcrypt($request->input('password'));
        $plant->latitude = $request->latitude;
        $plant->longitude = $request->longitude;
        $plant->city = $city;
        $plant->state = $state;
        $plant->country = $country;
        $plant->status = 1;
        $plant->plant_type = $request->rep_type;
        $plant->business_name = $request->input('manufacturer_name') ?? '';
        $plant->save();

        $manufacturer = new Manufacturer();
        $manufacturer->status = 0;
        $manufacturer->plant_id = $plant->id; // Associate plant with manufacturer
        $manufacturer->manufacturer_name = $request->input('manufacturer_name') ?? '';
        $manufacturer->full_name = $request->input('manufacturer_full_name') ?? '';
        $manufacturer->save();

        $plant->update(['manufacturer_id' => $manufacturer->id]);

        if ($request->hasFile('manufacturer_image')) {
            $file = $request->file('manufacturer_image');

            // Generate unique file name
            $name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

            // Move file to destination folder
            $file->move(public_path('upload/manufacturer-image'), $name);

            // Save image attribute to database
            ManufacturerAttributes::create([
                'manufacturer_id' => $manufacturer->id,
                'attribute_type' => 'Image',
                'attribute_name' => $name,
                'attribute_value' => $name,
                'plant_id' => $plant->id,
            ]);
        }

        Auth::guard('manufacturer')->login($plant);

        return redirect()->route('manufacturer.dashboard')->with('success', 'Manufacturer created successfully. Please log in.');
    } catch (\Exception $e) {
        return errorMsg("Exception -> " . $e->getMessage());
    }
}



    public function updateManufacturer(Request $request, $id)
    {
        // dd(Auth::user());
        // Define validation rules
        $rules = [
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|',
            'email' => 'nullable|email|max:255',
            'business_name' => 'nullable|string|max:255',
            'manufacturer_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:10',
            'manufacturer_type' => 'nullable|string|max:255',
            'manufacturer_image' => 'nullable|',
            'description' => 'nullable',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //dd($request->all());
        $user = Auth::user();
        // Update manufacturer
        $manufacturer = PlantLogin::where('id',$user->id)->first();
        //dd($manufacturer);
        // $manufacturer->full_name = $request->input('full_name');
        $manufacturer->phone = $request->input('phone'); // Update 'mobile' from 'phone' input field
        $manufacturer->email = $user->email;
        $manufacturer->business_name = $request->input('business_name');
        $manufacturer->about = $request->input('description');
        //$manufacturer->manufacturer_address = $request->input('location'); // Update 'manufacturer_address' from 'location' input field
        // $manufacturer->city = $request->input('city');
        // $manufacturer->state = $request->input('state');
        // $manufacturer->zipcode = $request->input('zipcode');
        // $manufacturer->manufacturer_type = $request->input('manufacturer_type');
        
        // Update password if provided
        // if ($request->filled('new_password') && $request->input('new_password') === $request->input('confirm_password')) {
        //     $manufacturer->password = bcrypt($request->input('new_password'));
        // }

        if ($request->hasFile('manufacturer_image')) {
            $plant = DB::table('plant_login')->where('id',$manufacturer->id)->first();
            // $plant['image']->delete();
            // Delete old image if exists
            if (!empty($manufacturer->manufacturer_image)) {
                $oldImagePath = public_path('upload/manufacturer-image/' . $manufacturer->manufacturer_image);
                
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete the old image file
                }
            }
    
            // Upload new image
            $file = $request->file('manufacturer_image');
            $name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/manufacturer-image'), $name);
    
            // Update image attribute in database
            DB::table('plant_login')
            ->where('id', $id)
            ->update(['image' => $name]);
        }

        $manufacturer->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }


    public function savePlant(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'plant_name' => 'nullable|string',
                'email' => 'nullable|string',
                'phone' => 'nullable|string',
                'description' => 'nullable|string',
                'full_address' => 'nullable|string',
                'latitude' => 'required_with:full_address|numeric',
                'longitude' => 'required_with:full_address|numeric',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'zipcode' => 'nullable|string',
                'shipping_cost' => 'nullable|string',
                'sales_manager' => 'nullable|array',
                'sales_manager.name.*' => 'nullable|string',
                'sales_manager.email.*' => 'nullable|string',
                'sales_manager.phone.*' => 'nullable|string',
                'sales_manager.designation.*' => 'nullable|string',
                'sales_manager.images.*' => 'nullable',
                'price_range' => 'nullable|string',
                'from_price_range' => 'nullable|string',
                'to_price_range' => 'nullable|string',
                'specification' => 'nullable|string',
                'type' => 'nullable|string',
                'web_link' => 'nullable',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $validated = $validator->validated();

            $specifications = $request->specifications ? implode(',', $request->specifications) : null;

            // Initialize the HTTP client
            $client = new Client();

            // Make the request to the Mapbox Geocoding API
            $response = $client->get('https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($request->full_address) . '.json', [
                'query' => [
                    'access_token' => 'pk.eyJ1IjoidXNlcnMxIiwiYSI6ImNsdGgxdnpsajAwYWcya25yamlvMHBkcGEifQ.qUy8qSuM_7LYMSgWQk215w',
                ],
            ]);
    
            // Parse the response
            $data = json_decode($response->getBody(), true);
            $features = $data['features'][0] ?? [];
            //dd($features);
            // Extract city, state, and country from the response
            $city = '';
            $state = '';
            $country = '';
    
            if (isset($data['features'][0])) {
                $features = $data['features'][0];
                $city = $state = $country = $features['text'] ?? ''; // Default to text if no specific context
                
                foreach ($features['context'] as $context) {
                    if (strpos($context['id'], 'place') === 0) {
                        $city = $context['text'];
                    } elseif (strpos($context['id'], 'region') === 0) {
                        $state = $context['text'];
                    } elseif (strpos($context['id'], 'country') === 0) {
                        $country = $context['text'];
                    }
                }
    
                // If no specific city found in context, use the text from the response
                $city = $city ?: $features['text'];
            } else {
                // Handle case where features are not available
                $city = $state = $country = 'Unknown';
            }

            // Create the new plant
            $plant = Plant::create([
                'plant_name' => $request->plant_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'description' => $request->description,
                'full_address' => $request->full_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'zipcode' => null,
                'price_range' => $request->price_range,
                'from_price_range' => $request->from_price_range,
                'to_price_range' => $request->to_price_range,
                'specification' => $specifications,
                'type' => $request->type,
                'web_link' => $request->web_link,
                'manufacturer_id' => Auth::user()->id,
                'shipping_cost'=> $request->shipping_cost,
                'status' => 0,
            ]);
            $user = Auth::user();

            $plants_login = PlantLogin::where('id',$user->id)->first();
           // dd($plants_login->plant_type);
            try {
                $name = 'Admin';
                $email = 'spencer@roane.com';
                $plantName = $plant->plant_name;
                $type = $plants_login->plant_type;
                $location = $plant->full_address;
                $business_name = $plants_login->business_name ?? 'N/A';
                Mail::to($email)->send(new ApprovalMail($plantName,$location,$type,$business_name));
            } catch (\Throwable $th) {
            }

           

            // if($plants_login['plant_type'] == 'plant_rep'){
            //     PlantLogin::where('id', $user->id)->update([
            //         'email' => $request->email,
            //         'plant_name' => $request->plant_name,
            //         'full_address' => $request->full_address,
            //         'longitude' => $request->longitude,
            //         'latitude' => $request->latitude,
            //         'phone' => $request->phone,
            //         'city' => $city,
            //         'state' => $state,
            //         'country' => $country,
            //     ]);
            // }
            

            // Handle sales managers
            if ($request->has('sales_manager')) {
                foreach ($request->sales_manager['name'] as $key => $value) {
                    if (!empty($value)) {
                        $name = ucfirst(strtolower($value));
                        $imagePath = null;
                        if ($request->hasFile("sales_manager.images.$key")) {
                            $file = $request->file("sales_manager.images.$key");
                            $imageName = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('upload/sales-manager-images'), $imageName);
                            $imagePath = $imageName;
                        }

                        PlantSalesManager::create([
                            'plant_id' => $plant->id,
                            'name' => $name,
                            'phone' => $request->sales_manager['phone'][$key],
                            'email' => $request->sales_manager['email'][$key],
                            'designation' => $request->sales_manager['designation'][$key],
                            'manufacturer_id' => Auth::user()->id,
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            // Handle images
            if ($request->hasfile('images')) {
                foreach ($request->file('images') as $file) {
                    $name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('upload/manufacturer-image'), $name);
                    PlantMedia::create([
                        'plant_id' => $plant->id,
                        'image_url' => $name
                    ]);
                }
            }
            if ($user->plant_type === 'corp_rep') {
                return redirect()->route('manufacturer.manage-locations')->with('success', 'Details successfully saved!');
            }
            return redirect()->route('ViewPlant', ['id' => $plant->id])->with('success', 'Details successfully saved!');
        } catch (\Exception $e) {
            return response('Exception => ' . $e->getMessage());
        }
    }



    public function updatePlant(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'plant_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'phone' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'full_address' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'price_range' => 'nullable|string',
                'to_price_range' => 'nullable|string',
                'from_price_range' => 'nullable|string',
                'type' => 'nullable|string',
                'web_link' => 'nullable|string',
                'specification' => 'nullable|string',
                'shipping_cost' => 'nullable|string',
                'sales_manager.name.*' => 'nullable|string',
                'sales_manager.designation.*' => 'nullable|string',
                'sales_manager.email.*' => 'nullable|string|email',
                'sales_manager.phone.*' => 'nullable|string',
                'sales_manager.images.*' => 'nullable',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $validated = $validator->validated();
            //dd($request->all());
            // Find the plant by ID
            $plant = Plant::findOrFail($id);
    
            // Update specifications if present
            $specifications = $request->specifications ? implode(',', $request->specifications) : null;
    
            // Initialize the HTTP client
            $client = new Client();
    
            // Make the request to the Mapbox Geocoding API
            $response = $client->get('https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($request->full_address) . '.json', [
                'query' => [
                    'access_token' => 'pk.eyJ1IjoidXNlcnMxIiwiYSI6ImNsdGgxdnpsajAwYWcya25yamlvMHBkcGEifQ.qUy8qSuM_7LYMSgWQk215w',
                ],
            ]);
    
            // Parse the response
            $data = json_decode($response->getBody(), true);
            $features = $data['features'][0] ?? [];
            //dd($features);
            // Extract city, state, and country from the response
            $city = '';
            $state = '';
            $country = '';
            if (isset($data['features'][0])) {
                $features = $data['features'][0];
                $city = $state = $country = $features['text'] ?? ''; // Default to text if no specific context
                
                foreach ($features['context'] as $context) {
                    if (strpos($context['id'], 'place') === 0) {
                        $city = $context['text'];
                    } elseif (strpos($context['id'], 'region') === 0) {
                        $state = $context['text'];
                    } elseif (strpos($context['id'], 'country') === 0) {
                        $country = $context['text'];
                    }
                }
    
                // If no specific city found in context, use the text from the response
                $city = $city ?: $features['text'];
            } else {
                // Handle case where features are not available
                $city = $state = $country = 'Unknown';
            }
    
            // Update plant details
            $plant->update([
                'plant_name' => $request->plant_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'description' => $request->description,
                'full_address' => $request->full_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'price_range' => $request->price_range,
                'from_price_range' => $request->from_price_range,
                'to_price_range' => $request->to_price_range,
                'specification' => $specifications,
                'type' => $request->type,
                'web_link' => $request->web_link,
                'shipping_cost' => $request->shipping_cost,
            ]);
    
            // Update PlantLogin details
            $user = Auth::user();


            
            $plants_login = PlantLogin::where('id',$user->id)->first();

            // if($plants_login['plant_type'] == 'plant_rep'){
            //     PlantLogin::where('id', $user->id)->update([
            //         'email' => $request->email,
            //         'plant_name' => $request->plant_name,
            //         'full_address' => $request->full_address,
            //         'longitude' => $request->longitude,
            //         'latitude' => $request->latitude,
            //         'phone' => $request->phone,
            //         'city' => $city,
            //         'state' => $state,
            //         'country' => $country,
            //     ]);
            // }
    
            // Update sales managers
            if ($request->has('sales_manager')) {
                // Delete existing sales managers
                DB::table('plant_sales_manager')->where('plant_id', $plant->id)->where('manufacturer_id', $user->id)->delete();
    
                // Create new sales managers
                foreach ($request->sales_manager['name'] as $key => $value) {
                    if (!empty($value)) {
                        $imagePath = null;
                        $name = ucfirst(strtolower($value));
    
                        // Check if a new file is uploaded for this sales manager
                        if ($request->hasFile("sales_manager.images.$key")) {
                            $file = $request->file("sales_manager.images.$key");
                            $imageName = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('upload/sales-manager-images'), $imageName);
                            $imagePath = $imageName;
                        } else {
                            // If no new image is uploaded, use the existing image from the hidden input
                            $imagePath = $request->sales_manager['existing_images'][$key] ?? null;
                        }
    
                        PlantSalesManager::create([
                            'plant_id' => $plant->id,
                            'name' => $name,
                            'phone' => $request->sales_manager['phone'][$key],
                            'email' => $request->sales_manager['email'][$key],
                            'designation' => $request->sales_manager['designation'][$key],
                            'manufacturer_id' => $user->id,
                            'image' => $imagePath,
                        ]);
                    }
                }
            }
    
            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('upload/manufacturer-image'), $name);
                    PlantMedia::create([
                        'plant_id' => $plant->id,
                        'image_url' => $name
                    ]);
                }
            }
            if ($user->plant_type === 'corp_rep') {
                return redirect()->route('manufacturer.manage-locations')->with('success', 'Plant details updated successfully.');
            }
    
            return redirect()->route('ViewPlant', ['id' => $plant->id])->with('success', 'Plant details updated successfully.');
        } catch (\Exception $e) {
            return errorMsg('Exception => ' . $e->getMessage());
        }
    }



    public function updateManufacturerPassword(Request $request, $id)
    {
        // Define validation rules for password
        $rules = [
            'old_password' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        // Retrieve the manufacturer
        $manufacturer = PlantLogin::findOrFail($id);

        // Check if old password matches
        // if (!Hash::check($request->input('old_password'), $manufacturer->password)) {
        //     return response()->json(['errors' => ['The old password is incorrect.']], 422);
        // }

        // Update password
        $manufacturer->password = bcrypt($request->input('password'));
        $manufacturer->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }



    public function ManufacturerProfile(Request $request)
    {
        $users = Auth::user();
        // dd($users);
        $user = Manufacturer::where('plant_id',$users->id)->first();
        $attributes = DB::table('manufacturer_attributes')->where('manufacturer_id',$user->id)->first();
        //dd($attributes);
        //dd($user);
        return view('manufacturer.profile',compact('user','attributes','users'));
    }


    public function AddPlant(Request $request)
    {
        $user = Auth::user();
        //dd($user);
        // $userSettings = DB::table('settings')->where('manufacturer_id', $user->id)->first();
        // $selectedCountry = $userSettings->country ?? 'United States'; 
        $specifications = DB::table('specifications')->where('manufacturer_id', $user->id)->get();
        return view('manufacturer.add-plant',compact('user','specifications'));
    }



    public function ViewPlant(Request $request, $id)
    {
        $user = Auth::user();
        // $userSettings = DB::table('settings')->where('manufacturer_id', $user->id)->first();
        // $selectedCountry = $userSettings->country ?? 'United States'; 
        // Fetch plant details, sales managers, and media
        $plantData = DB::table('plant')
            ->leftJoin('plant_sales_manager', 'plant_sales_manager.plant_id', '=', 'plant.id')
            ->leftJoin('plant_media', 'plant_media.plant_id', '=', 'plant.id')
            ->select(
                'plant.id as plant_id',
                'plant.plant_name',
                'plant.email',
                'plant.phone',
                'plant.description',
                'plant.full_address',
                'plant.latitude',
                'plant.longitude',
                'plant.zipcode',
                'plant.price_range',
                'plant.from_price_range',
                'plant.to_price_range',
                'plant.specification',
                'plant.type',
                'plant.manufacturer_id',
                'plant.web_link',
                'plant.status',
                'plant.is_approved',
                'plant.shipping_cost',
                'plant.created_at',
                'plant.updated_at',
                'plant_sales_manager.id as manager_id',
                'plant_sales_manager.name as manager_name',
                'plant_sales_manager.email as manager_email',
                'plant_sales_manager.phone as manager_phone',
                'plant_sales_manager.designation as manager_designation',
                'plant_sales_manager.image as manager_image',
                'plant_media.id as media_id',
                'plant_media.image_url'
            )
            ->where('plant.id', $id)
            ->where('plant.manufacturer_id', $user->id)
            ->get();
    
        $plant = null;
        $salesManagers = [];
        $images = [];
        $specifications = [];
    
        foreach ($plantData as $data) {
            if ($plant === null) {
                $plant = [
                    'id' => $data->plant_id,
                    'plant_name' => $data->plant_name,
                    'email' => $data->email,
                    'status' => $data->status,
                    'is_approved' => $data->is_approved,
                    'phone' => $data->phone,
                    'web_link' => $data->web_link,
                    'description' => $data->description,
                    'full_address' => $data->full_address,
                    'latitude' => $data->latitude,
                    'longitude' => $data->longitude,
                    'zipcode' => $data->zipcode,
                    'price_range' => $data->price_range,
                    'from_price_range' => $data->from_price_range,
                    'to_price_range' => $data->to_price_range,
                    'specification' => $data->specification, // Assuming this is a comma-separated string of specification IDs
                    'type' => $data->type,
                    'manufacturer_id' => $data->manufacturer_id,
                    'shipping_cost' => $data->shipping_cost,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                    'sales_managers' => [],
                    'images' => [],
                    'specifications' => [], // Initialize specifications array
                ];
            }
    
            if ($data->manager_id !== null && !isset($salesManagers[$data->manager_id])) {
                $salesManagers[$data->manager_id] = [
                    'id' => $data->manager_id,
                    'name' => $data->manager_name,
                    'email' => $data->manager_email,
                    'phone' => $data->manager_phone,
                    'designation' => $data->manager_designation,
                    'image' => $data->manager_image,
                ];
            }
    
            if ($data->media_id !== null && !isset($images[$data->media_id])) {
                $images[$data->media_id] = [
                    'id' => $data->media_id,
                    'image_url' => $data->image_url
                ];
            }
    
            // Fetch specifications if available
            if (!empty($data->specification)) {
                $specificationIds = explode(',', $data->specification);
                // Example: Fetch specifications from another table
                $specifications = DB::table('specifications')
                    ->whereIn('id', $specificationIds)
                    ->get();
            }
        }
    
        if ($plant !== null) {
            $plant['sales_managers'] = array_values($salesManagers);
            $plant['images'] = array_values($images);
            $plant['specifications'] = $specifications; // Add specifications to the plant array
        }
    
       // dd($plant);
        return view('manufacturer.plant-details', compact('user', 'plant'));
    }




    public function EditPlant(Request $request, $id)
    {
        $user = Auth::user();
        $specificationss = DB::table('specifications')->where('manufacturer_id',$user->id)->get();
        // $userSettings = DB::table('settings')->where('manufacturer_id', $user->id)->first();
        // $selectedCountry = $userSettings->country ?? 'United States'; 
        // Fetch plant details, sales managers, and media
        $plantData = DB::table('plant')
            ->leftJoin('plant_sales_manager', 'plant_sales_manager.plant_id', '=', 'plant.id')
            ->leftJoin('plant_media', 'plant_media.plant_id', '=', 'plant.id')
            ->select(
                'plant.id as plant_id',
                'plant.plant_name',
                'plant.email',
                'plant.phone',
                'plant.description',
                'plant.full_address',
                'plant.latitude',
                'plant.longitude',
                'plant.zipcode',
                'plant.price_range',
                'plant.from_price_range',
                'plant.to_price_range',
                'plant.specification',
                'plant.type',
                'plant.manufacturer_id',
                'plant.shipping_cost',
                'plant.created_at',
                'plant.web_link',
                'plant.updated_at',
                'plant_sales_manager.id as manager_id',
                'plant_sales_manager.name as manager_name',
                'plant_sales_manager.email as manager_email',
                'plant_sales_manager.phone as manager_phone',
                'plant_sales_manager.designation as manager_designation',
                'plant_sales_manager.image as manager_image',
                'plant_media.id as media_id',
                'plant_media.image_url'
            )
            ->where('plant.id', $id)
            ->where('plant.manufacturer_id', $user->id)
            ->get();
    
        $plant = null;
        $salesManagers = [];
        $images = [];
        $specifications = [];
    
        foreach ($plantData as $data) {
            if ($plant === null) {
                $plant = [
                    'id' => $data->plant_id,
                    'plant_name' => $data->plant_name,
                    'email' => $data->email,
                    'phone' => $data->phone,
                    'description' => $data->description,
                    'full_address' => $data->full_address,
                    'latitude' => $data->latitude,
                    'longitude' => $data->longitude,
                    'zipcode' => $data->zipcode,
                    'price_range' => $data->price_range,
                    'from_price_range' => $data->from_price_range,
                    'to_price_range' => $data->to_price_range,
                    'specification' => $data->specification, // Assuming this is a comma-separated string of specification IDs
                    'type' => $data->type,
                    'manufacturer_id' => $data->manufacturer_id,
                    'web_link' => $data->web_link,
                    'shipping_cost' => $data->shipping_cost,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                    'sales_managers' => [],
                    'images' => [],
                    'specifications' => [], // Initialize specifications array
                ];
            }
    
            if ($data->manager_id !== null && !isset($salesManagers[$data->manager_id])) {
                $salesManagers[$data->manager_id] = [
                    'id' => $data->manager_id,
                    'name' => $data->manager_name,
                    'email' => $data->manager_email,
                    'phone' => $data->manager_phone,
                    'designation' => $data->manager_designation,
                    'image' => $data->manager_image,
                ];
            }
    
            if ($data->media_id !== null && !isset($images[$data->media_id])) {
                $images[$data->media_id] = [
                    'id' => $data->media_id,
                    'image_url' => $data->image_url
                ];
            }
    
            // Fetch specifications if available
            if (!empty($data->specification)) {
                $specificationIds = explode(',', $data->specification);
                // Example: Fetch specifications from another table
                $specifications = DB::table('specifications')
                    ->whereIn('id', $specificationIds)
                    ->get();
            }
        }
    
        if ($plant !== null) {
            $plant['sales_managers'] = array_values($salesManagers);
            $plant['images'] = array_values($images);
            $plant['specifications'] = $specifications; // Add specifications to the plant array
        }
    
       // dd($plant);
        return view('manufacturer.edit-plant', compact('user', 'plant','specificationss'));
    }


    public function logout(Request $request) {
        Auth::logout();
        return redirect()->route('manufacturer.login');
    }


    public function enquiries(Request $request) {
        $user = Auth::user();
       //dd($request->all());
        // $userSettings = DB::table('settings')->where('manufacturer_id', $user->id)->first();
        // $selectedCountry = $userSettings->country ?? 'United States'; 
        $search = $request->search ? $request->search : '';
        $date = $request->date ? $request->date : '';
        $statusFilter = $request->status;
        $manufacturer = $request->manufacturer_id;
        $new_enquiriess = DB::table('contact_manufacturer')
                        ->leftJoin('plant', 'contact_manufacturer.plant_id', '=', 'plant.id')
                        ->leftJoin('plant_login','plant_login.id','=','plant.manufacturer_id')
                        ->where('plant_login.id', $user->id)
                        ->leftJoin('users', 'contact_manufacturer.user_id', '=', 'users.id')
                        ->when($statusFilter, function ($query, $statusFilter) {
                            if ($statusFilter == 'read') {
                                return $query->where('contact_manufacturer.status', 1);
                            } elseif ($statusFilter == 'unread') {
                                return $query->where('contact_manufacturer.status', 0);
                            }
                        })
                        ->when($search, function ($query, $search) {
                            return $query->where(function ($query) use ($search) {
                                $query->where('contact_manufacturer.user_name', 'like', '%' . $search . '%')
                                      ->orWhere('contact_manufacturer.phone_no', 'like', '%' . $search . '%')
                                      ->orWhere('contact_manufacturer.company_name', 'like', '%' . $search . '%')
                                      ->orWhere('plant.plant_name', 'like', '%' . $search . '%');
                            });
                        })
                        ->when($date, function ($query, $date) {
                            return $query->whereDate('contact_manufacturer.created_at', $date);
                        })
                        ->when($manufacturer, function ($query, $manufacturer) {
                            return $query->where('plant.plant_name', 'like', '%' . $manufacturer . '%');
                        })
                        ->orderBy('contact_manufacturer.id', 'DESC')
                        ->select(
                            'contact_manufacturer.id as enquiry_id',
                            'contact_manufacturer.user_name as enquiry_name',
                            'contact_manufacturer.phone_no as enquiry_phone',
                            'contact_manufacturer.email as enquiry_mail',
                            'contact_manufacturer.company_name as company_name',
                            'contact_manufacturer.type as type',
                            'users.id as user_id',
                            'contact_manufacturer.message',
                            'contact_manufacturer.status',
                            'contact_manufacturer.created_at',
                            'contact_manufacturer.location',
                            'users.fullname',
                            'users.email',
                            'users.mobile',
                            'users.business_name',
                        );
            $data = $new_enquiriess->get();
            $count = count($data);
            $new_enquiries = $new_enquiriess->paginate(10);
            $plants = DB::table('plant')->where('manufacturer_id',$user->id) ->orderBy('plant_name', 'asc')->get();
        //dd($new_enquiries);
        if ($request->filled('download')) {
            // dd($data_enquiries);
            $fileName = $manufacturer ? $manufacturer . '_inquiries.xls' : 'plant_inquiries.xls';
             return Excel::download(new ManufacturerEnquiriesExport($data), $fileName);
         }
        return view('manufacturer.enquiry',compact('new_enquiries','user','search','date','statusFilter','plants','count'));
    }


    public function manageLocations(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();
        $search = $request->search ? $request->search : '';
        
        // Fetch plants associated with the authenticated user
        $plants = Plant::where('manufacturer_id', $user->id);
        if ($request->filled('search')) {
            // Apply search across multiple fields (plant_name, full_address, city, and state)
            $plants->where(function($query) use ($search) {
                $query->where('plant_name', 'LIKE', '%' . $search . '%')
                      ->orWhere('full_address', 'LIKE', '%' . $search . '%')
                      ->orWhere('city', 'LIKE', '%' . $search . '%')
                      ->orWhere('state', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            if ($request->status == 1) {
                // Approved plants (status 1 and is_approved 'Y')
                $plants->where('plant.status', 1)
                            ->where('plant.is_approved', 'Y');
            } elseif ($request->status == 3) {
                // Unapproved plants (status 0 and is_approved 'N')
                $plants->where('plant.status', 0)
                            ->where('plant.is_approved', 'N');
            } elseif ($request->status == 4) {
                // Pending plants (status 0 or NULL, and is_approved NULL)
                $plants->where(function($q) {
                    $q->where('plant.status', 0)
                    ->orWhereNull('plant.status');
                })
                ->whereNull('plant.is_approved');
            }
        }
        $plantCount = $plants->count();
        $plants = $plants->get();
        // Loop through each plant to fetch state and country based on latitude and longitude
        foreach ($plants as $plant) {
            $lat = $plant->latitude;
            $lng = $plant->longitude;

            // Fetch state and country details using Mapbox Geocoding API
            $response = Http::get('https://api.mapbox.com/geocoding/v5/mapbox.places/' . $lng . ',' . $lat . '.json', [
                'access_token' => 'pk.eyJ1IjoidXNlcnMxIiwiYSI6ImNsdGgxdnpsajAwYWcya25yamlvMHBkcGEifQ.qUy8qSuM_7LYMSgWQk215w',
                'types' => 'place',
                'language' => 'en',
            ]);

            // Check if request was successful and parse response
            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['features'][0]['place_name'])) {
                    $placeName = $data['features'][0]['place_name'];
                    $placeParts = explode(', ', $placeName);

                    $plant->city = isset($placeParts[0]) ? $placeParts[0] : null; // Assuming state is the second part
                    $plant->state = isset($placeParts[1]) ? $placeParts[1] : null; // Assuming country is the third part
                } else {
                    // Handle if no place name is found
                    $plant->city = null;
                    $plant->state = null;
                }
            } else {
                // Handle API request failure (optional)
                $plant->city = null;
                $plant->state = null;
            }

            $images = DB::table('plant_media')
            ->where('plant_id', $plant->id)
            ->select('id', 'image_url') // Adjust fields as per your table structure
            ->get();

            $plant->images = $images;
        }
        if ($request->filled('download')) {
            // dd($data_enquiries);
             return Excel::download(new CorporatePlantsExport($plants), 'plants.xls');
         }

        // Pass the fetched data to the view 'manufacturer.manage-locations'
        return view('manufacturer.manage-locations', compact('plants', 'user','search','plantCount'));
    }


    public function delete(Request $request)
    {
        $plantId = $request->input('plant_id');
        
        // Perform deletion logic, e.g., using Eloquent
        $plant = Plant::find($plantId);
        if (!$plant) {
            return response()->json(['error' => 'Plant not found'], 404);
        }
        PlantSalesManager::where('plant_id', $plantId)->delete();
        PlantMedia::where('plant_id', $plantId)->delete();
        $plant->delete();
        session()->flash('success', 'Plant deleted successfully!');

        return response()->json(['message' => 'Plant deleted successfully']);
    }

    public function settings(Request $request)
    {
        $user = Auth::user();
        $countryFlags = config('country_flags');
        return view('manufacturer.settings',compact('user','countryFlags'));
    }


    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        // Validate the request
        $request->validate([
            'country' => 'required|string|max:255'
        ]);
        //dd($request->all());
        // Save the selected country in the settings table
        DB::table('settings')->updateOrInsert(
            ['manufacturer_id' => $user->id],
            ['country' => $request->country]
        );

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }


    public function deletePhoto($id)
    {
        try {
            // Find the image by ID
            $image = PlantMedia::findOrFail($id);
            
            // Delete the image file from the storage
            $imagePath = public_path('upload/manufacturer-image/' . $image->image_url);
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


    public function deleteSpecPhoto($id)
    {
        try {
            // Find the image by ID
            $image = Specifications::findOrFail($id);
            
            // Delete the image file from the storage
            $imagePath = public_path('upload/specification-image/' . $image->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $image->image = null;
            // Delete the image record from the database
             $image->save();

            // Return a success response
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Handle the error
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


    public function deleteProfilePhoto($id)
    {
        try {
            // Find the image by ID
            $plantLogin = PlantLogin::findOrFail($id);

            // Path to the image file
            $imagePath = public_path('upload/manufacturer-image/' . $plantLogin->image);

            // Delete the image file from the storage if it exists
            if (file_exists($imagePath) && !empty($plantLogin->image)) {
                unlink($imagePath);
            }
            $plantLogin->image = null;
            // Update the image field to null in the database
            $plantLogin->save();

            // Return a success response
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Handle the error
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


    public function forget()
    {
        return view("manufacturer.forget");
    }
    public function reset()
    {
        if (request()->has("token")) {
            $exists =  DB::table('password_resets')->where([

                'token' => request('token')

            ])->first();
            if ($exists) {
                $email = $exists->email;
                return view("manufacturer.reset", compact('email'));
            }
        }
        return redirect("manufacturer.login");
    }
    public function submitResetPasswordForm(Request $request)
    {
        // try {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        if (PlantLogin::where('email', $request->email)->count()) {
            $user = PlantLogin::where('email', $request->email)
                ->update(['password' => Hash::make($request->password)]);
        }


        return response()->json(['message' => 'Password changed successfully.',  'success' => true, 'status' => 200], 200);
    }
    public function submitResetPasswordFormEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $admin = PlantLogin::where("email", $request->email)->count();

            if ($admin) {

                $user = PlantLogin::where("email", $request->email)->first();

                if (request()->has("otp") && request('otp') != "") {

                    if (request('otp') == session('otp')) {
                        if ((time() - session('time')) <= 600) {
                            // Delete any existing token for this email
                            DB::table('password_resets')->where('email', $request->email)->delete();

                            $token = uniqid();
                            DB::table('password_resets')->insert([
                                'email' => $request->email,
                                'token' => $token,
                                'created_at' => Carbon::now()
                            ]);

                            return response()->json(['message' => route("manufacturer.reset.password", 'token=' . $token), 'redirect' => true, 'success' => true, 'status' => 200], 200);
                        } else {
                            return response()->json(['message' => 'Otp Expired.', 'success' => false, 'status' => 201], 201);
                        }
                    } else {
                        return response()->json(['message' => 'Please enter a valid OTP.', 'success' => false, 'status' => 201], 201);
                    }
                }

                $token = rand(100000, 999999);
                session(['otp' => $token, 'time' => time()]);
                
                try {
                    $name = $user->plant_name;
                    Mail::to($request->email)->send(new ForgetPassword($user, $token,$name));
                } catch (\Throwable $th) {
                    // Log the error to see the issue
                    //dd($th);
                    return response()->json([
                        'message' => 'There was an issue sending the email. Please check your mail settings.',
                        'status' => 500,
                        'success' => false,
                    ], 500);
                }
                // return redirect()->back()->with("success", "We have just sent you Verification Code for Password Reset");
                return response()->json(['message' => 'We have just sent you an one time password for resetting the password.', 'redirect' => false, 'token' => $token, 'success' => true, 'status' => 200], 200);
            } else {
                // return redirect()->back()->with("error", "User does not exist in our records.");
                return response()->json(['message' => 'User does not exist in our records.', 'success' => false, 'status' => 201], 201);
            }
        } catch (Exception $e) {
            // return redirect()->back()->with("error", $e->getMessage());
            return response()->json(['message' => $e->getMessage(), 'success' => false, 'status' => 201], 201);
        }

    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $exists = PlantLogin::where('email', $email)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function importExcel(Request $request)
    {
        try {
            // Validate file input
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            ]);
            $mfsId = $request->mfs_id;
            // Import the file
            set_time_limit(0);
            Excel::import(new PlantImport($mfsId), $request->file('file'));

            // Return success response
            return response()->json(['success' => true, 'message' => 'Plants imported successfully.']);
        } catch (\Exception $e) {
            // Log error and return error response
            Log::error('Error during import: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error during import: ' . $e->getMessage()]);
        }
    }



    public function saveContact(Request $request)
    {
        // Validate only the email field as required
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Save the data to the database, using null for optional fields
        Contact::create([
            'first_name' => $request->input('first_name', null),
            'last_name' => $request->input('last_name', null),
            'email' => $request->input('email'),
            'phone' => $request->input('phone', null),
            'message' => $request->input('message', null),
            'created_at' => Carbon::now(),
        ]);

        // Return a success response
        return redirect()->back()->with('success', 'Your form has been sent successfully!');
    }



    public function ManufacturerimportExcel(Request $request)
    {
        try {
            // Validate file input
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            ]);
            // Import the file
            set_time_limit(0);
            Excel::import(new PlantManufacturerImport, $request->file('file'));

            // Return success response
            return response()->json(['success' => true, 'message' => 'Plants imported successfully.']);
        } catch (\Exception $e) {
            // Log error and return error response
            Log::error('Error during import: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error during import: ' . $e->getMessage()]);
        }
    }

    
}
