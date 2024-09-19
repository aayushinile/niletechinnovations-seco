<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Community;
use App\Models\CommunityAttributes;
use App\Models\CommunityPropertyManagers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\ContactManufacturer;
use App\Models\Manufacturer;
use App\Models\ManufacturerAttributes;
use App\Models\Plant;
use App\Models\PlantLogin;
use App\Models\PlantMedia;
use App\Models\PlantSalesManager;
use App\Models\ShippingCost;
use App\Models\MileSettings;
use App\Models\Specifications;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommunityOwnersExport;
use App\Exports\PlantsExport;
use App\Exports\ContactedManufacturerExport;
use App\Exports\EnquiriesExport;
use App\Exports\CorporateManufacturersExport;
use Mail;
use App\Mail\ForgetPassword;
use App\Mail\ResetCredentials;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class AdminController extends Controller
{
    public function login()
    {


        return view('admin.auth.login');
    }



    public function authenticate(Request $request)
    {
        // Validate the user's input
        $credentials = $request->validate([
            'email' => 'required|email|exists:admins,email',
            'password' => 'required',
        ]);



        if (Auth::guard("admin")->attempt($request->only(['email', 'password', 'type']))) {

            $admin = Admin::where("email", $request->email)->first();
            if ($admin->type == 'admin') {
                Auth::guard("admin")->login($admin);
                // return redirect(route("admin.dashboard"));

                return response()->json(['message' => 'Logged In Successfully. ', 'redirect' => true, 'route' => route("admin.dashboard"), 'status' => 200]);

                // Redirect to admin dashboard
            }
            //  elseif ($admin->type == 'customer') {
            //     Auth::guard("customer")->login($admin);
            //     return redirect()->intended('/profile1'); // Redirect to customer profile
            // }
        }

        // if (Auth::guard("customer")->attempt($request->only(['email', 'password', 'type']))) {
        //     // Authenticate the admin

        //     $admin = Admin::where("email", $request->email)->first();
        //     Auth::guard("customer")->login($admin);

        //     return redirect()->intended('/profile'); // Redirect to the intended admin dashboard route
        // }

        // Authentication failed
        return response()->json(['message' => 'Invalid credentials', 'status' => 201]);

        // return redirect()->back()->withErrors(['email' => 'Invalid credentials']);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        // Optionally, you can flash a message or perform any other action here
        // Example: $request->session()->flash('message', 'Logged out successfully.');

        return redirect(route("admin.login")); // Redirect to login page or any other route
    }
    public function dashboard()
    {
        $plants = DB::table('plant')->count();
        $plant_with_manufacturer = DB::table('plant')
            ->count();
        $enquiries = DB::table('contact_manufacturer')
        ->count();
        $total_manufacturer = DB::table('plant_login')->count();
        $total_manufacturer_corp = DB::table('plant_login')->where('plant_type','corp_rep')->count();
        $total_manufacturer_plant = DB::table('plant_login')->where('plant_type','plant_rep')->count();
        $total_community = DB::table('community')->where('status', 1)->count();
        $manufacturer_request = DB::table('plant_login')->where('status', 1)->orderBy("id", 'desc')->take(5)
            ->get();
        $community = Community::whereNotNull("user_id")->groupBy("user_id")->pluck("user_id")->toArray();

        $owners = User::whereIn("type", [1, 2])->count();

        $plant_groups = Plant::select('full_address', 'plant_name', DB::raw('count(*) as total'))
            ->groupBy('full_address', 'plant_name')
            ->orderBy("total", "desc")
            ->take(5)
            ->get();
        return view("admin.dashboard", compact('plants', 'plant_groups', 'owners', 'total_manufacturer', 'total_community', 'manufacturer_request', 'plant_with_manufacturer','enquiries','total_manufacturer_plant','total_manufacturer_corp'));
    }


    public function communityOwners(Request $request)
    {
        // Get the list of community user IDs
        $community = Community::whereNotNull("user_id")->groupBy("user_id")->pluck("user_id")->toArray();
        
        // Build the query with the necessary filters
        $ownersQuery = User::whereIn('type', [1, 2])
                 ->whereIn('status', [0, 1])
            ->when($request->has('search'), function ($query) use ($request) {
                $keyword = trim($request->search);
                return $query->where(function ($query) use ($keyword) {
                    $query->where("business_name", "LIKE", "%$keyword%")
                        ->orWhere("email", "LIKE", "%$keyword%")
                        ->orWhere("mobile", "LIKE", "%$keyword%");
                });
            })
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where("status", $request->status);
            })
            
            ->when($request->has('date'), function ($query) use ($request) {
                return $query->whereDate("created_at", $request->date);
            })

            ->when($request->filled('type') && $request->type !== 'all', function ($query) use ($request) {
                return $query->where("type", $request->type);
            });

        // Define the owners variable for export
        if ($request->has("search") || $request->has("status") || $request->has("date")) {
            $owners = $ownersQuery->orderBy("id", "desc")->get();
            $owners_2 = $owners;
        } else {
            $owners_2 = $ownersQuery->orderBy("id", "desc")->get();
            $owners = $ownersQuery->orderBy("id", "desc")->paginate(10);
        }

        // Export to Excel if download request is filled
        if ($request->filled('download')) {
            $filename = 'community_owners_' . date('m-d-Y') . '.xls';
            return Excel::download(new CommunityOwnersExport($owners_2), $filename);
        }

        // Return the view with paginated data
        return view("admin.owners.index", compact('owners'));
    }
    public function communityOwnersShow(Request $request, $slug)
    {

        $id = decrypt($slug);
        $owner = User::find($id);
        $community = DB::table('community')
        ->leftJoin('community_attributes', 'community.id', '=', 'community_attributes.community_id')
        ->where('community.user_id', $id)
        ->orderBy('community.id', 'DESC')
        ->select(
            'community.id as community_id', // Select and alias the community id
            'community.community_name',
            'community.community_address',           // Select the location from community
            'community.description', 
            'community_attributes.value'    // Select the value from community_attributes
        )
        ->get();

        $manufracturers = ContactManufacturer::where("user_id", $id)->pluck("plant_id")->toArray();
        $contact_m = DB::table('plant')
        ->select('plant.*', 'plant_media.image_url')
        ->leftJoin('plant_media', 'plant.id', '=', 'plant_media.plant_id')
        ->whereIn('plant.id', $manufracturers)
        ->get();

        $saved_locations = DB::table('locations')
        ->where('user_id', $id)
        ->get();


        foreach ($contact_m as  $item) {

            $image_attribute = ManufacturerAttributes::where("manufacturer_id", $item->id)->where("attribute_name", "image")->first();
            if ($image_attribute) {
                $item->image = $image_attribute->attribute_value;
            } else {
                $item->image = null;
            }
        }
        return view("admin.owners.owners-detail", compact('community', 'owner', 'contact_m','saved_locations'));
    }


    public function export(Request $request)
    {
        // Retrieve the ID from the query string
        $id = $request->query('id');
        
        // Find the owner by ID
        $owner = User::find($id);

        // Check if the owner exists
        if (!$owner) {
            return redirect()->back()->withErrors('Owner not found');
        }

        // Get the list of manufacturers related to the owner
        $manufacturers = ContactManufacturer::where("user_id", $id)->pluck("plant_id")->toArray();

        // Fetch plant data with optional media
        $contact_m = DB::table('plant')
            ->select('plant.*', 'plant_media.image_url')
            ->leftJoin('plant_media', 'plant.id', '=', 'plant_media.plant_id')
            ->whereIn('plant.id', $manufacturers)
            ->get();
        // Download the Excel file
       // dd($owner);
        return Excel::download(new ContactedManufacturerExport($contact_m, $owner), 'contacted_manufacturer.xls');
    }


    public function communityShow(Request $request, $slug)
    {

        $id = decrypt($slug);

        $community = Community::find($id);
        $managers = CommunityPropertyManagers::where("community_id", $id)->get();
        $images = CommunityAttributes::where("community_id", $id)->where("attribute_name", "image")->pluck("value")->toArray();

        return view("admin.owners.community-detail", compact('community', 'managers', 'images'));
    }


    public function manufracturers(Request $request)
    {
        //dd($request->all());
        $manufracturers = Plant::whereNotNull("plant_name")
        ->whereHas('plantLogin', function ($query) {
            $query->where('plant_type', 'plant_rep');
        })
        ->when($request->has('search'), function ($query) use ($request) {
            $keyword = trim($request->search);
            return $query->where(function ($query) use ($keyword) {
                $query->where("plant_name", "LIKE", "%$keyword%")
                    ->orWhere("phone", "LIKE", "%$keyword%")
                    ->orWhere("email", "LIKE", "%$keyword%")
                    ->orWhere("full_address", "LIKE", "%$keyword%")
                    ->orWhere("state", "LIKE", "%$keyword%")
                    ->orWhere("city", "LIKE", "%$keyword%")
                    ->orWhere("country", "LIKE", "%$keyword%");
            });
        })
        ->when($request->has('date'), function ($query) use ($request) {
            return $query->whereDate("created_at", $request->date);
        })
        ->when($request->has('status'), function ($query) use ($request) {
            $status = $request->status;
            if ($status !== '2') {
                // Filter by status if it's not 'SHOW ALL'
                return $query->whereHas('plantLogin', function ($subQuery) use ($status) {
                    $subQuery->where('status', $status);
                });
            } else {
                // If 'SHOW ALL' is selected, show both Active and Inactive records
                return $query->whereHas('plantLogin', function ($subQuery) {
                    $subQuery->whereIn('status', [0, 1]);
                });
            }
        })
        ->orderBy("id", "desc");

        if ($request->has("search") || $request->has("status") || $request->has("date")) {
            $manufracturers = $manufracturers->get();
            $manufacturer_2 = $manufracturers;
        } else {
            $manufacturer_2 = $manufracturers->get();
            $manufracturers = $manufracturers->paginate(10);
            
        }
        //dd($manufracturers);
        $total = Plant::whereHas('plantLogin', function ($query) {
            $query->where('plant_type', 'plant_rep');
        })->count();
        
        if ($request->filled('download')) {
            //dd($manufacturer_2);
            return Excel::download(new PlantsExport($manufacturer_2), 'plants.xls');
        }

        return view("admin.manufracturers.index", compact('manufracturers', 'total'));
    }




    public function plantexport(Request $request)
    {
        $manufracturers = Plant::where('manufacturer_id',$request->id)
        ->orderBy("id", "desc");
        $manufacturer_2 = $manufracturers->get();
        return Excel::download(new PlantsExport($manufacturer_2), 'plants.xls');
    }



    public function Corporatemanufracturers(Request $request)
    {
        $manufracturers = PlantLogin::where('plant_type', 'corp_rep')
            ->when($request->has('search'), function ($query) use ($request) {
                $keyword = trim($request->search);
                return $query->where(function ($query) use ($keyword) {
                    $query->where("plant_name", "LIKE", "%$keyword%")
                        ->orWhere("business_name", "LIKE", "%$keyword%")
                        ->orWhere("phone", "LIKE", "%$keyword%")
                        ->orWhere("email", "LIKE", "%$keyword%")
                        ->orWhere("full_address", "LIKE", "%$keyword%")
                        ->orWhere("state", "LIKE", "%$keyword%")
                        ->orWhere("city", "LIKE", "%$keyword%")
                        ->orWhere("country", "LIKE", "%$keyword%");
                });
            })
            ->when($request->has('date'), function ($query) use ($request) {
                return $query->whereDate("created_at", $request->date);
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $status = $request->status;
                if ($status !== '2') {
                    // Filter by status if it's not 'SHOW ALL'
                    return $query->where("status", $status);
                } else {
                    // If 'SHOW ALL' is selected, show both Active and Inactive records
                    return $query->WhereIn("status", [0,1]);
                }
            })
            ->orderBy("id", "desc");

        if ($request->has("search") || $request->has("status") || $request->has("date")) {
            $manufracturers = $manufracturers->get();
            $manufacturer_2 = $manufracturers;
        } else {
            $manufacturer_2 = $manufracturers->get();
            $manufracturers = $manufracturers->paginate(10);
        }

        //dd($manufracturers);
        $total = PlantLogin::where('plant_type', 'corp_rep')->count();
       
        if ($request->filled('download')) {
            return Excel::download(new CorporateManufacturersExport($manufacturer_2), 'corporate_representative.xls');
        }

        return view("admin.manufracturers.corporateindex", compact('manufracturers', 'total'));
    }

    
    public function manufracturers_requests(Request $request)
    {
        $manufracturers = Manufacturer::whereNull("status")->when($request->has('search'), function ($query) use ($request) {
            $keyword = trim($request->search);
            return $query->where("full_name", "LIKE", "%$keyword%")->orWhere("email", "LIKE", "%$keyword%")->orWhere("mobile", "LIKE", "%$keyword%");
        })->when($request->has('status'), function ($query) use ($request) {

            return $query->where("status", $request->status);
        })->orderBy("id", "desc")->paginate(10);
        return view("admin.manufracturers.requests", compact('manufracturers'));
    }
    public function manufracturers_requests_approve(Request $request, $id)
    {
        $mf = Manufacturer::find($id);
        if ($mf) {
            $mf->status = 1;
            $mf->save();
        }
        return back()->with("success", 'Request Approved Successfully');
    }
    public function manufracturersShow(Request $request, $slug)
    {
        $id = decrypt($slug);
        $manufacturer = null;
        $plant = Plant::where('id', $id)->first();
        $mfs = PlantLogin::where('id',$plant->manufacturer_id)->first();

        // $manufracturers = ContactManufacturer::where("plant_id", $plant->id)->pluck("user_id")->toArray();
        // //dd($manufracturers);
        // $contact_m = DB::table('users')
        // ->whereIn('id', $manufracturers)
        // ->get();

        // foreach ($contact_m as  $item) {

        //     $image_attribute = ManufacturerAttributes::where("manufacturer_id", $item->id)->where("attribute_name", "image")->first();
        //     if ($image_attribute) {
        //         $item->image = $image_attribute->attribute_value;
        //     } else {
        //         $item->image = null;
        //     }
        // }

        if (!empty($plant)) {
            $images = PlantMedia::where('plant_id', $plant->id)->get();
            $sales_managers = PlantSalesManager::where('plant_id', $plant->id)->get();
            $specificationIds = explode(',', $plant->specification);
            $specifications = Specifications::whereIn('id', $specificationIds)->get();
            $manufracturers = ContactManufacturer::where("plant_id", $plant->id)->pluck("user_id")->toArray();
            $contact_m = DB::table('users')
                ->whereIn('id', $manufracturers)
                ->get();
            $manufacturers = ContactManufacturer::where("plant_id", $plant->id)->orderby('id','DESC')->get();
        } else {
            $images = collect(); // Use an empty collection instead of an array
            $sales_managers = collect(); // Same for sales managers
            $specifications = collect(); // Same for specifications
            $contact_m = collect(); // Same for contacted manufacturers 
            $manufacturers = collect();
        }
        return view("admin.manufracturers.detail", compact('mfs', 'plant', 'images', 'sales_managers', 'specifications', 'manufacturer','contact_m','manufacturers'));
    }




    public function EditPlant(Request $request, $id)
    {
        // dd($id);
        $plantid = $id;
        $specificationss = [];
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
        return view('admin.manufracturers.edit-plant-admin', compact('plant','specificationss'));
    }

    public function updatePlantAdmin(Request $request, $id)
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
            // $user = Auth::user();


            
            // $plants_login = PlantLogin::where('id',$user->id)->first();

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
                DB::table('plant_sales_manager')->where('plant_id', $plant->id)->where('manufacturer_id', $plant->manufacturer_id)->delete();
    
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
                            'manufacturer_id' => $plant->manufacturer_id,
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
            $manufacturer = null;
            $mfs = PlantLogin::where('id',$plant->manufacturer_id)->first();
            if (!empty($plant)) {
                $images = PlantMedia::where('plant_id', $plant->id)->get();
                $sales_managers = PlantSalesManager::where('plant_id', $plant->id)->get();
                $specificationIds = explode(',', $plant->specification);
                $specifications = Specifications::whereIn('id', $specificationIds)->get();
                $manufracturers = ContactManufacturer::where("plant_id", $plant->id)->pluck("user_id")->toArray();
                $contact_m = DB::table('users')
                    ->whereIn('id', $manufracturers)
                    ->get();
                $manufacturers = ContactManufacturer::where("plant_id", $plant->id)->orderby('id','DESC')->get();
            } else {
                $images = collect(); // Use an empty collection instead of an array
                $sales_managers = collect(); // Same for sales managers
                $specifications = collect(); // Same for specifications
                $contact_m = collect(); // Same for contacted manufacturers
                $manufacturers = collect();
            }
            return view('admin.manufracturers.detail',compact('mfs', 'plant', 'images', 'sales_managers', 'specifications', 'manufacturer','contact_m','manufacturers'))
            ->with('success', 'Plant details updated successfully.');
        } catch (\Exception $e) {
            return errorMsg('Exception => ' . $e->getMessage());
        }
    }



    public function manufracturersCorpShow(Request $request, $slug)
    {
        $id = decrypt($slug);

        $plants = DB::table('plant')
                ->leftJoin('plant_media', 'plant_media.plant_id', '=', 'plant.id')
                ->where('plant.manufacturer_id', $id)
                ->orderBy('plant.id', 'DESC')
                ->select(
                    'plant.id as plant_id', // Select and alias the community id
                    'plant.plant_name',
                    'plant.full_address',
                    'plant.status', 
                    'plant.phone',   // Select the value from community_attributes
                    'plant.email',
                    'plant.is_approved',
                )
                ->distinct()
                ->paginate(10);

            $count = DB::table('plant')
            ->leftJoin('plant_media', 'plant_media.plant_id', '=', 'plant.id')
            ->where('plant.manufacturer_id', $id)
            ->orderBy('plant.id', 'DESC')
            ->select(
                'plant.id as plant_id', // Select and alias the community id
                'plant.plant_name',
                'plant.full_address',
                'plant.status', 
                'plant.phone',   // Select the value from community_attributes
                'plant.email',
                'plant.is_approved',
            )
            ->distinct()
            ->count();

        //$plant = Plant::where('id', $id)->first();
        $mfs = PlantLogin::where('id',$id)->first();

        // $manufracturers = ContactManufacturer::where("plant_id", $plant->id)->pluck("user_id")->toArray();
        // //dd($manufracturers);
        // $contact_m = DB::table('users')
        // ->whereIn('id', $manufracturers)
        // ->get();

        // foreach ($contact_m as  $item) {

        //     $image_attribute = ManufacturerAttributes::where("manufacturer_id", $item->id)->where("attribute_name", "image")->first();
        //     if ($image_attribute) {
        //         $item->image = $image_attribute->attribute_value;
        //     } else {
        //         $item->image = null;
        //     }
        // }

        if (!empty($plant)) {
            $images = PlantMedia::where('plant_id', $plant->id)->get();
            $sales_managers = PlantSalesManager::where('plant_id', $plant->id)->get();
            $specificationIds = explode(',', $plant->specification);
            $specifications = Specifications::whereIn('id', $specificationIds)->get();
            $manufracturers = ContactManufacturer::where("plant_id", $plant->id)->pluck("user_id")->toArray();
            $contact_m = DB::table('users')
                ->whereIn('id', $manufracturers)
                ->get();
        } else {
            $images = collect(); // Use an empty collection instead of an array
            $sales_managers = collect(); // Same for sales managers
            $specifications = collect(); // Same for specifications
            $contact_m = collect(); // Same for contacted manufacturers
        }
        return view("admin.manufracturers.corpdetails", compact('mfs', 'plants','images','count'));
    }



    // anshul coded this functions
    public function enquiries(Request $request)
    {
        //dd($request->all());
       $search = $request->search;
       $date = $request->date;
       //$count = ContactManufacturer::whereNotNull("user_id")->count();
       $mergedData = ContactManufacturer::whereNotNull("user_id")
       ->join('plant', 'contact_manufacturer.plant_id', '=', 'plant.id')
       ->when($request->has('search'), function ($query) use ($request) {
           $keyword = trim($request->search);
           $query->where(function ($query) use ($keyword) {
               $query->where("contact_manufacturer.user_name", "LIKE", "%$keyword%")
                   ->orWhere("contact_manufacturer.email", "LIKE", "%$keyword%")
                   ->orWhere("contact_manufacturer.company_name", "LIKE", "%$keyword%")
                   ->orWhere("contact_manufacturer.location", "LIKE", "%$keyword%")
                   ->orWhere("phone_no", "LIKE", "%$keyword%")
                   ->orWhere("plant.plant_name", "LIKE", "%$keyword%");
           });
       })
       ->when($request->has('manufacturer_id'), function ($query) use ($request) {
           $manufacturerName = trim($request->manufacturer_id);
           $query->where("plant.plant_name", "LIKE", "%$manufacturerName%");
       })
       ->when($request->has('date'), function ($query) use ($request) {
           $query->whereDate("contact_manufacturer.created_at", $request->input('date'));
       })
       ->select('contact_manufacturer.*', 'plant.plant_name')
       ->orderBy("contact_manufacturer.id", "desc");
           // dd($request->manufacturer_id);
        if ($request->has("search") || $request->has("manufacturer_id") || $request->has("date")) {
            $mergedData = $mergedData->orderBy("contact_manufacturer.id", "desc")->get();
            $data_enquiries = $mergedData;
        } else {
            $data_enquiries =  $mergedData->orderBy("contact_manufacturer.id", "desc")->get();
            $mergedData = $mergedData->orderBy("contact_manufacturer.id", "desc")->paginate(10);
        }
        $count = count($mergedData);

        foreach ($mergedData as $item) {
            $item->plant_name = Plant::find($item->plant_id) ? Plant::find($item->plant_id)->plant_name : "N/A";
        }

        // $developers = ContactManufacturer::where('user_id', $user_id)
        //     ->with('community')
        //     ->get();

        // dd("abcd");

        // dd($developers);
        // return view('enquiries', $developers);

        // $developers = contact_manufacturer::with('community')->get();


        // $developers = contact_manufacturer::all();
        // return view('enquiries', ['developers' => $developers]);
        if ($request->filled('download')) {
           // dd($data_enquiries);
           $fileName = $request->manufacturer_id ? $request->manufacturer_id . '_inquiries.xls' : 'inquiries.xls';
            return Excel::download(new EnquiriesExport($data_enquiries), $fileName);
        }
        
        $pls = Plant::select('plant_name')
            ->groupBy('plant_name')          // Group by 'plant_name' to avoid duplicates
            ->orderBy('plant_name', 'asc')   // Order by 'plant_name' in ascending order (alphabetical)
            ->get();
        $total = ContactManufacturer::whereNotNull("user_id")->count();
        return view('admin.enquiries', ['mergedData' => $mergedData, 'pls' => $pls, 'total' => $total,'search' => $search,'count' => $count]);
    }
    public function profile()
    {
        // Fetch currently authenticated admin user
        $admin = auth('admin')->user(); // Assuming you're using 'admin' guard

        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Admin::find(auth('admin')->user()->id);

        // Update name, email, and phone
        $admin->fullname = $request->input('fullname');
        $admin->email = $request->input('email');
        $admin->mobile = $request->input('mobile');

        // Handle profile image upload if provided
        if ($request->hasFile('file')) {
            // Store the uploaded file and update the admin's profile_image=null; attribute
            $profileImage = $request->file('file');
            $name = uniqid() . "." . $profileImage->getClientOriginalExtension();
            $path = $profileImage->move('profile_images', $name); // Adjust the storage path as needed
            $admin->profile_image = 'profile_images/' . $name;
        }

        $admin->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }



    public function changePassword(Request $request)
    {
        // Validate the form data

        $validator = Validator::make($request->all(), [
            'password' => 'nullable',
            'new_password' => 'required|min:4|confirmed',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }
        // Get the authenticated admin
        $admin = Admin::find(auth('admin')->user()->id);



        // Check if the old password matches
        // if (!Hash::check($request->password, $admin->password)) {
        //     return redirect()->back()->with('error', 'The old password is incorrect.');
        // }

        // Update the password
        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }




    public function updateManufacturerPassword(Request $request)
    {
        $rules = [
            'password' => 'required|string|min:6|confirmed',
            'email' => 'nullable|email', // Validate the email format
        ];
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }
    
        $manufacturer = PlantLogin::findOrFail($request->id);
        $password = $request->input('password');
        $email = $request->input('email');
    
        // Update email and password
        if ($email) {
            $manufacturer->email = $email;
        }
    
        $manufacturer->password = bcrypt($password);
        $manufacturer->save();
    
        // Send the email if the email address is present
        if ($email) {
            Mail::to($email)->send(new ResetCredentials($password,$email));
        }
    
        return response()->json([
            'message' => 'Credentials updated successfully and an email has been sent to the email address.',
            'email' => $email
        ], 200);
    }
    public function settings()
    {
        $shippingCosts = ShippingCost::all();
        $miles = MileSettings::where('id',1)->first();
        // Transform the collection into a key-value array based on type
        $formattedData = $shippingCosts->keyBy('type');
        return view('admin.settings', ['mergedData' => $formattedData,'mile' => $miles]);
    }
    public function save_setting(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'single_wide_cost' => 'nullable',
            'double_wide_cost' => 'nullable',
            'single_double_wide_cost' => 'nullable',
             'set_miles' => 'nullable|numeric'
        ]);
        DB::beginTransaction();
        try {
            // Array of type and corresponding cost values
            $types = [
                'sw' => $validated['single_wide_cost'],
                'dw' => $validated['double_wide_cost'],
                'sw_dw' => $validated['single_double_wide_cost'],
            ];
            foreach ($types as $type => $cost) {
                //dd($cost);
                // Check if a record with the given type exists
                $record = ShippingCost::where('type', $type)->update([
                    'shipping_cost' => $cost,
                ]);
                // dd($record);
            }
            //dd($validated['set_miles']);

            MileSettings::where('id', 1)->update([
                'miles' => $validated['set_miles'],
            ]);

            DB::commit();
            // Redirect back with a success message
            return back()->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            // Handle any errors
            return back()->withErrors('An error occurred while saving settings: ' . $e->getMessage());
        }
    }
    public function set_status(Request $request)
    {
        // Validate the form data
        $request->validate([
            'status' => 'required',
            'table' => 'required',
            'id' => 'required', // confirmed means new_password_confirmation must match new_password
        ]);

        try {
            $data = DB::table($request->table)->where("id", $request->id)->first();
            if ($data) {
                DB::table($request->table)->where("id", $request->id)->update(['status' => $request->status]);

                return back()->with('success', 'Status updated successfully');
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
        // Get the authenticated admin


        // Check if the old password matches
        return back()->with('error', 'Something went wrong');
    }
    public function forget()
    {
        return view("admin.auth.forget");
    }
    public function reset()
    {
        if (request()->has("token")) {
            $exists =  DB::table('password_resets')->where([

                'token' => request('token')

            ])->first();
            if ($exists) {
                $email = $exists->email;
                return view("admin.auth.reset", compact('email'));
            }
        }
        return redirect("admin.login");
    }
    public function submitResetPasswordForm(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required'
            ]);
    
            // Check if the email exists in the Admin table
            $userExists = Admin::where('email', $request->email)->exists();
    
            if ($userExists) {
                // Update the user's password
                Admin::where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);
    
                // Return a successful response
                return response()->json(['message' => 'Password changed successfully.', 'success' => true, 'status' => 200], 200);
            } else {
                // Return an error if the email does not exist
                return response()->json(['message' => 'Email not found.', 'success' => false, 'status' => 404], 404);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage(), 'success' => false, 'status' => 500], 500);
        }
    }
    public function submitResetPasswordFormEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);
            $admin =  Admin::where("email",   $request->email)->count();

            if ($admin) {

                $user =  Admin::where("email",   $request->email)->first();

                // date_default_timezone_set(getTimeZone($user->id));
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

                            return response()->json(['message' => route("admin.reset.password", 'token=' . $token), 'redirect' => true, 'success' => true, 'status' => 200], 200);
                        } else {
                            return response()->json(['message' => 'Otp Expired.', 'success' => false, 'status' => 201], 201);
                        }
                    } else {
                        return response()->json(['message' => 'Please enter a valid OTP.', 'success' => false, 'status' => 201], 201);
                    }
                }


                $token = rand(100000, 999999);
                session(['otp' => $token, 'time' => time()]);
                // try {
                //     Mail::to($request->email)->send(new ForgetPassword(['data' => ['user' => $user, 'token' => $token]]));
                // } catch (\Throwable $th) {
                //     return response()->json(['message' => 'Unable to send email. Please check SMTP details', 'status' => 200, 'success' => false,], 200);
                // }
                // return redirect()->back()->with("success", "We have just sent you Verification Code for Password Reset");
                try {
                    $name = 'Admin';
                    Mail::to($request->email)->send(new ForgetPassword($user, $token,$name));
                } catch (\Throwable $th) {
                    // Log the error to see the issue
                    return response()->json([
                        'message' => 'There was an issue sending the email. Please check your mail settings.',
                        'status' => 500,
                        'success' => false,
                    ], 500);
                }
                return response()->json(['message' => 'We have just sent you an one time password for resetting the password.', 'redirect' => false, 'success' => true, 'status' => 200], 200);
            } else {
                // return redirect()->back()->with("error", "User does not exist in our records.");
                return response()->json(['message' => 'User does not exist in our records.',  'success' => false, 'status' => 201], 201);

                return response()->json(['message' => 'User does not exist in our records.', 'success' => false, 'status' => 200], 301);
            }
        } catch (Exception $e) {
            // return redirect()->back()->with("error", $e->getMessage());

            return response()->json(['message' => $e->getMessage(), 'success' => false, 'status' => 201], 201);
        }
    }

    public function removeProfile()
    {
        $ADMIN = Admin::find(auth("admin")->user()->id);
        $ADMIN->profile_image = null;
        $ADMIN->save();
        return back()->with('success', "Profile picture removed successfully");
    }




    public function saveManufacturerAdmin(Request $request)
    {
        try {
            // dd('in');
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
            // dd($request->all());
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

            // $manufacturer = new Manufacturer();
            // $manufacturer->status = 0;
            // $manufacturer->plant_id = $plant->id; // Associate plant with manufacturer
            // $manufacturer->manufacturer_name = $request->input('manufacturer_name') ?? '';
            // $manufacturer->full_name = $request->input('manufacturer_full_name') ?? '';
            // $manufacturer->save();

            // $plant->update(['manufacturer_id' => $manufacturer->id]);

            // if ($request->hasFile('manufacturer_image')) {
            //     $file = $request->file('manufacturer_image');

            //     // Generate unique file name
            //     $name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

            //     // Move file to destination folder
            //     $file->move(public_path('upload/manufacturer-image'), $name);

            //     // Save image attribute to database
            //     ManufacturerAttributes::create([
            //         'manufacturer_id' => $manufacturer->id,
            //         'attribute_type' => 'Image',
            //         'attribute_name' => $name,
            //         'attribute_value' => $name,
            //         'plant_id' => $plant->id,
            //     ]);
            // }

            $manufracturers = PlantLogin::where('plant_type', 'corp_rep')
            ->orderBy("id", "desc");
            $manufacturer_2 = $manufracturers->get();
            $manufracturers = $manufracturers->paginate(10);

        //dd($manufracturers);
        $total = PlantLogin::where('plant_type', 'corp_rep')->count();
            return view("admin.manufracturers.corporateindex", compact('manufracturers', 'total'));
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }
}
