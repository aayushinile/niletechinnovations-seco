<?php

namespace App\Http\Controllers;

use App\Models\ContactManufacturer;
use App\Models\Plant;
use App\Models\PlantLogin;
use App\Models\Specifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\PlantSalesManager;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function saveSpecifications(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'values' => 'required|string|max:255',
            'image' => 'nullable',
        ]);

        try {
            // $fileName = time().'.'.$request->file->extension();  
            //$request->file->move(public_path('uploads'), $fileName);

            $specification = Specifications::create([
                'name' => $request->name,
                'values' => $request->values,
                'file' => '',
                'manufacturer_id' => Auth::user()->id,
                'status' => 1,
            ]);

            if ($request->hasFile('image')) {
                // dd('in');
                $file = $request->file('image');

                // Generate unique file name
                $name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

                // Move file to destination folder
                $file->move(public_path('upload/specification-image'), $name);

                // Save image attribute to database
                $specification->update([
                    'image' => $name,
                ]);
            }
            $user = Auth::user();

            $specifications = DB::table('specifications')->where('manufacturer_id', $user->id)->get();
            $view = view('manufacturer.ajax.specifications', compact('specifications'))->render();
            return response()->json(['status' => true, 'view' => $view, 'message' => 'Specification saved successfully.', 'specification' => $specification]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to save specification: ' . $e->getMessage()]);
        }
    }



    public function updateSpecifications(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'values' => 'nullable|string',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $specification = Specifications::find($id);
        if (is_null($specification)) {
            return response()->json(['error' => 'Specification not found'], 404);
        }

        $specification->update([
            'name' => $request->name,
            'values' => $request->values,
        ]);

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            // Generate unique file name
            $name = 'IMG_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

            // Move file to destination folder
            $file->move(public_path('upload/specification-image'), $name);

            // Save image attribute to database
            $specification->update([
                'image' => $name,
            ]);
        }

        $user = Auth::user();
        $specifications = DB::table('specifications')->where('manufacturer_id', $user->id)->get();
        $view = view('manufacturer.ajax.specifications', compact('specifications'))->render();

        return response()->json(['success' => 'Specification updated successfully.', 'view' => $view, 'specification' => $specification], 200);
    }


    public function removeSpecification($id)
    {
        try {
            $specification = Specifications::findOrFail($id);

            $specification->delete();

            return response()->json(['success' => true, 'message' => 'Specification deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete specification: ' . $e->getMessage()]);
        }
    }


    public function deleteSalesManager($id)
    {
        try {
            // Find the sales manager by ID and delete it
            $salesManager = PlantSalesManager::findOrFail($id);
            $salesManager->delete();

            return response()->json(['message' => 'Sales manager deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete sales manager.'], 500);
        }
    }

    public function toggleRequestStatus(Request $request)
    {
        if ($request->has('request_id')) {
            $enquiry = ContactManufacturer::find($request->input('request_id'));
            if ($enquiry) {
                $enquiry->status = $request->input('status');
                $enquiry->save();
                return response()->json(['success' => true, 'message' => 'Request status changed successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Something went wrong']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong']);
        }
    }


    public function activatePlant(Request $request)
    {
        try {
            $plantId = $request->input('plant_id');
            $plant = PlantLogin::where('id', $plantId)->first();
            if (!$plant) {
                return response()->json(['status' => false, 'message' => 'Plant not found']);
            }

            // Update the plant status
            $plant->status = 1;
            $plant->save();

            return response()->json(['status' => true, 'message' => 'Plant activated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while activating the plant']);
        }
    }

    public function inactivatePlant(Request $request)
    {
        try {
            $plantId = $request->input('plant_id');
            $plant = PlantLogin::where('id', $plantId)->first();
            if (!$plant) {
                return response()->json(['status' => false, 'message' => 'Plant not found']);
            }

            // Update the plant status
            $plant->status = 0;
            $plant->save();

            return response()->json(['status' => true, 'message' => 'Plant activated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while activating the plant']);
        }
    }



    public function set_status(Request $request)
    {
        $ids = explode(',', $request->input('plant_ids'));
        $status = $request->input('status'); // Inactivate the plants
        // return $status;
        if (is_array($ids) && count($ids) > 0) {
            if ($status == 0) {
                DB::table('plant_login')
                    ->whereIn('id', $ids)
                    ->update(['status' => 0]);
            }


            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid request.']);
    }


    public function set_statuss(Request $request)
    {
        $ids = explode(',', $request->input('plant_ids'));
        $status = $request->input('status'); // Inactivate the plants
        // return $status;
        if (is_array($ids) && count($ids) > 0) {
            DB::table('plant_login')
                ->whereIn('id', $ids)
                ->update(['status' => 1]);


            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid request.']);
    }
}
