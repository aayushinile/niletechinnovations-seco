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
use App\Models\Specifications;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;

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
        $total_manufacturer = DB::table('plant_login')->count();
        $total_community = DB::table('community')->where('status', 1)->count();
        $manufacturer_request = DB::table('plant_login')->where('status', 1)->orderBy("id", 'desc')->take(5)
            ->get();
        $community = Community::whereNotNull("user_id")->groupBy("user_id")->pluck("user_id")->toArray();

        $owners = User::where("type", 2)->whereIn('id', $community)->count();

        $plant_groups = Plant::select('full_address', 'plant_name', DB::raw('count(*) as total'))
            ->groupBy('full_address', 'plant_name')
            ->orderBy("total", "desc")
            ->take(3)
            ->get();
        return view("admin.dashboard", compact('plants', 'plant_groups', 'owners', 'total_manufacturer', 'total_community', 'manufacturer_request', 'plant_with_manufacturer'));
    }


    public function communityOwners(Request $request)
    {

        $community = Community::whereNotNull("user_id")->groupBy("user_id")->pluck("user_id")->toArray();
        $owners = User::where("type", 2)->when($request->has('search'), function ($query) use ($request) {
            $keyword = trim($request->search);
            return $query->where("business_name", "LIKE", "%$keyword%")->orWhere("email", "LIKE", "%$keyword%")->orWhere("mobile", "LIKE", "%$keyword%");
        })->when($request->has('status'), function ($query) use ($request) {

            return $query->where("status", $request->status);
        })->when($request->has('date'), function ($query) use ($request) {

            return $query->whereDate("created_at", $request->date);
        })->whereIn("id", $community);
        if ($request->has("search") || $request->has("status") || $request->has("date")) {
            $owners = $owners->orderBy("id", "desc")->get();
        } else {
            $owners = $owners->orderBy("id", "desc")->paginate(10);
        }




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

        foreach ($contact_m as  $item) {

            $image_attribute = ManufacturerAttributes::where("manufacturer_id", $item->id)->where("attribute_name", "image")->first();
            if ($image_attribute) {
                $item->image = $image_attribute->attribute_value;
            } else {
                $item->image = null;
            }
        }
        return view("admin.owners.owners-detail", compact('community', 'owner', 'contact_m'));
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
        $manufracturers = PlantLogin::whereNotNull("status")->when($request->has('search'), function ($query) use ($request) {
            $keyword = trim($request->search);
            return $query->where("plant_name", "LIKE", "%$keyword%")->orWhere("phone", "LIKE", "%$keyword%")->orWhere("email", "LIKE", "%$keyword%")->orWhere("full_address", "LIKE", "%$keyword%");
        })->when($request->has('status'), function ($query) use ($request) {

            return $query->where("status", $request->status);
        })->when($request->has('date'), function ($query) use ($request) {

            return $query->whereDate("created_at", $request->date);
        })->orderBy("id", "desc");
        if ($request->has("search") || $request->has("status") || $request->has("date")) {
            $manufracturers = $manufracturers->get();
        } else {
            $manufracturers = $manufracturers->paginate(10);
        }
        //dd($manufracturers);
        $total = PlantLogin::count();

        return view("admin.manufracturers.index", compact('manufracturers', 'total'));
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
        $mfs = PlantLogin::find($id);
        $manufacturer = Manufacturer::where('id', $mfs->manufacturer_id)->first();
        $plant = Plant::where('manufacturer_id', $mfs->id)->first();
        if (!empty($plant)) {
            $images = PlantMedia::where('plant_id', $plant->id)->get();
            $sales_managers = PlantSalesManager::where('plant_id', $plant->id)->get();
            $specificationIds = explode(',', $plant->specification);
            $specifications = Specifications::whereIn('id', $specificationIds)->get();
        } else {
            $images = [];
            $sales_managers = [];
            $specifications = [];
        }
        return view("admin.manufracturers.detail", compact('mfs', 'plant', 'images', 'sales_managers', 'specifications', 'manufacturer'));
    }



    // anshul coded this functions
    public function enquiries(Request $request)
    {

        $mergedData = ContactManufacturer::whereNotNull("user_id")
            ->when($request->has('search'), function ($query) use ($request) {
                $keyword = trim($request->search);
                return $query->where("user_name", "LIKE", "%$keyword%")->orWhere("email", "LIKE", "%$keyword%")->orWhere("location", "LIKE", "%$keyword%")->orWhere("phone_no", "LIKE", "%$keyword%");
            })->when($request->has('manufacturer_id'), function ($query) use ($request) {
                return $query->where("plant_id", $request->manufacturer_id);
            })->when($request->has('date'), function ($query) use ($request) {

                return $query->whereDate("created_at", $request->input('date'));
            });
           // dd($request->manufacturer_id);
        if ($request->has("search") || $request->has("manufacturer_id") || $request->has("date")) {
            $mergedData = $mergedData->orderBy("id", "desc")->get();
        } else {
            $mergedData = $mergedData->orderBy("id", "desc")->paginate(10);
        }


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

        $pls = Plant::orderBy("id", 'desc')->get();
        $total = ContactManufacturer::whereNotNull("user_id")->count();
        return view('admin.enquiries', ['mergedData' => $mergedData, 'pls' => $pls, 'total' => $total]);
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
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $manufacturer = PlantLogin::findOrFail($request->id);

        $manufacturer->password = bcrypt($request->input('password'));
        $manufacturer->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }
    public function settings()
    {
        $shippingCosts = ShippingCost::all();
        // Transform the collection into a key-value array based on type
        $formattedData = $shippingCosts->keyBy('type');
        return view('admin.settings', ['mergedData' => $formattedData]);
    }
    public function save_setting(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'single_wide_cost' => 'nullable',
            'double_wide_cost' => 'nullable',
            'single_double_wide_cost' => 'nullable',
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
            DB::commit();
            // Redirect back with a success message
            return back()->with('success', 'Settings saved successfully');
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
        // try {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        if (Admin::where('email', $request->email)->count()) {
            $user = Admin::where('email', $request->email)
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
            $admin =  Admin::where("email",   $request->email)->count();

            if ($admin) {

                $user =  Admin::where("email",   $request->email)->first();

                // date_default_timezone_set(getTimeZone($user->id));
                if (request()->has("otp") && request('otp') != "") {

                    if (request('otp') == session('otp')) {
                        if ((time() - session('time')) <= 600) {
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
                return response()->json(['message' => 'We have just sent you an one time password for resetting the password.' . $token, 'redirect' => false, 'token' => $token, 'success' => true, 'status' => 200], 200);
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
        return back()->with('success', "Profile Picture removed successfully");
    }
}
