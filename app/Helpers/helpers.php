<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Manufacturer;


if (!function_exists('successMsg')) {
    function successMsg($msg, $data = [])
    {
        return response()->json(['status' => true, 'message' => $msg, 'data' => $data]);
    }
}

if (!function_exists('errorMsg')) {
    function errorMsg($msg, $data = [])
    {
        return response()->json(['status' => false, 'message' => $msg, 'data' => $data]);
    }
}

if (!function_exists('encrypt_decrypt')) {
    function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
        // hash
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
}

/* Upload Image */
if (!function_exists('imageUpload')) {
    function imageUpload($request, $path, $name)
    {
        if ($request->file($name)) {
            $imageName = 'IMG_' . date('Ymd') . '_' . date('His') . '_' . rand(1000, 9999) . '.' . $request->image->extension();
            $request->image->move(public_path($path), $imageName);
            return $imageName;
        }
    }
}

/* Handle and path accoding to local and live */
if (!function_exists('assets')) {
    function assets($path)
    {
        //return asset('public/'.$path); /* For live server */
        return asset($path);/* For local server(When project run on local comment first path) */
    }
}

/*Calculate Distance in KM accoding to pickup_lat_long and drop_lat_long */
if (!function_exists('getDistanceBetweenPoints')) {
    function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
    }
}

if (!function_exists('random_alphanumeric')) {
    function random_alphanumeric() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ12345689';
        $my_string = '';
        $length = 10;
        for ($i = 0; $i < $length; $i++) {
        $pos = random_int(0, strlen($chars) -1);
        $my_string .= substr($chars, $pos, 1);
        }
        return $my_string;
    }
}