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
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Mail\ApprovedPlantMail;

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
            $plants = DB::table('plant')->where('manufacturer_id', $user->id)->get();
            $plantSpecificationIds = [];
            foreach ($plants as $plant) {
                $specIds = explode(',', $plant->specification ?? '');
                $plantSpecificationIds = array_merge($plantSpecificationIds, $specIds);
            }
            $plantSpecificationIds[] = $specification->id;

            // Remove duplicates from the plantSpecificationIds array
            $plantSpecificationIds = array_unique($plantSpecificationIds);
            $specifications = DB::table('specifications')->where('manufacturer_id', $user->id)->get();
            $view = view('manufacturer.ajax.specifications', compact('specifications','plantSpecificationIds'))->render();
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
        $plants = DB::table('plant')->where('manufacturer_id', $user->id)->get();
        $plantSpecificationIds = [];
            foreach ($plants as $plant) {
                $specIds = explode(',', $plant->specification ?? '');
                $plantSpecificationIds = array_merge($plantSpecificationIds, $specIds);
            }
            $plantSpecificationIds[] = $specification->id;

            // Remove duplicates from the plantSpecificationIds array
            $plantSpecificationIds = array_unique($plantSpecificationIds);
        
        $specifications = DB::table('specifications')->where('manufacturer_id', $user->id)->get();
        $view = view('manufacturer.ajax.specifications', compact('specifications','plantSpecificationIds'))->render();

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




    public function toggleUserRequestStatus(Request $request)
    {
        if ($request->has('request_id')) {
            $enquiry = User::find($request->input('request_id'));
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
            if ($request->has('plant_id')) {
                // Ensure the plant_ids is an array, converting it if it's a comma-separated string
                $ids = $request->input('plant_id');
    
                if (is_string($ids)) {
                    $ids = explode(',', $ids);  // Convert comma-separated string to array
                }
    
                // Check if the array is not empty
                if (!empty($ids)) {
                    // Get the new status from the request
                    $status = 1;
    
                    // Prepare the update data
                    $updateData = ['status' => $status];
    
                    // Check if the status is 1 and set the is_approved column to 'Y'
                    if ($status == 1) {
                        $updateData['is_approved'] = 'Y';
                    }
                    // Check if the status is 0 and set the is_approved column to 'N'
                    elseif ($status == 0) {
                        $updateData['is_approved'] = 'N';
                    }
    
                    // Use the Plant model to update the status and is_approved column for all plants whose ID is in the array
                    Plant::whereIn('id', $ids)->update($updateData);
                    $plants = Plant::whereIn('id', $ids)->get();
                    foreach ($plants as $plant) {
                        $subject = $status == 1 ? 'ShowSearch - Plant Details Approved' : 'ShowSearch - Plant Details Unapproved';  // Set dynamic subject based on status
                        $plantEmail = $plant->email;  // Assuming 'email' is a field in the plant table

                        try {
                            // Send mail to the plant's email
                            Mail::to($plantEmail)->send(new ApprovedPlantMail($status, $subject, $plant));
                        } catch (\Throwable $th) {
                        }
                    }
                    return response()->json(['success' => true, 'message' => 'Request status changed successfully for all selected plants']);
                } else {
                    return response()->json(['success' => false, 'message' => 'No valid plant IDs provided']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Something went wrong']);
            }
        } catch (\Exception $e) {
            // Log the error or return the error message for debugging
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function inactivatePlant(Request $request)
    {
        try {
            if ($request->has('plant_id')) {
                // Ensure the plant_ids is an array, converting it if it's a comma-separated string
                $ids = $request->input('plant_id');
    
                if (is_string($ids)) {
                    $ids = explode(',', $ids);  // Convert comma-separated string to array
                }
    
                // Check if the array is not empty
                if (!empty($ids)) {
                    // Get the new status from the request
                    $status = 0;
    
                    // Prepare the update data
                    $updateData = ['status' => $status];
    
                    // Check if the status is 1 and set the is_approved column to 'Y'
                    if ($status == 1) {
                        $updateData['is_approved'] = 'Y';
                    }
                    // Check if the status is 0 and set the is_approved column to 'N'
                    elseif ($status == 0) {
                        $updateData['is_approved'] = 'N';
                    }
    
                    // Use the Plant model to update the status and is_approved column for all plants whose ID is in the array
                    Plant::whereIn('id', $ids)->update($updateData);
                    $plants = Plant::whereIn('id', $ids)->get();
                    foreach ($plants as $plant) {
                        $subject = $status == 1 ? 'ShowSearch - Plant Details Approved' : 'ShowSearch - Plant Details Unapproved';  // Set dynamic subject based on status
                        $plantEmail = $plant->email;  // Assuming 'email' is a field in the plant table

                        try {
                            // Send mail to the plant's email
                            Mail::to($plantEmail)->send(new ApprovedPlantMail($status, $subject, $plant));
                        } catch (\Throwable $th) {
                        }
                    }
                    return response()->json(['success' => true, 'message' => 'Request status changed successfully for all selected plants']);
                } else {
                    return response()->json(['success' => false, 'message' => 'No valid plant IDs provided']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Something went wrong']);
            }
        } catch (\Exception $e) {
            // Log the error or return the error message for debugging
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }



    public function set_status(Request $request)
{
    try {
        if ($request->has('plant_ids')) {
            // Ensure the plant_ids is an array, converting it if it's a comma-separated string
            $ids = $request->input('plant_ids');

            if (is_string($ids)) {
                $ids = explode(',', $ids);  // Convert comma-separated string to array
            }

            // Check if the array is not empty
            if (!empty($ids)) {
                // Get the new status from the request
                $status = $request->input('status');

                // Prepare the update data
                $updateData = ['status' => $status];

                // Check if the status is 1 and set the is_approved column to 'Y'
                if ($status == 1) {
                    $updateData['is_approved'] = 'Y';
                }
                // Check if the status is 0 and set the is_approved column to 'N'
                elseif ($status == 0) {
                    $updateData['is_approved'] = 'N';
                }

                
                // Use the Plant model to update the status and is_approved column for all plants whose ID is in the array
                Plant::whereIn('id', $ids)->update($updateData);
                $plants = Plant::whereIn('id', $ids)->get();
                foreach ($plants as $plant) {
                    $subject = $status == 1 ? 'ShowSearch - Plant Details Approved' : 'ShowSearch - Plant Details Unapproved';  // Set dynamic subject based on status
                    $plantEmail = $plant->email;  // Assuming 'email' is a field in the plant table

                    try {
                        // Send mail to the plant's email
                        Mail::to($plantEmail)->send(new ApprovedPlantMail($status, $subject, $plant));
                    } catch (\Throwable $th) {
                    }
                }

                return response()->json(['success' => true, 'message' => 'Request status changed successfully for all selected plants']);
            } else {
                return response()->json(['success' => false, 'message' => 'No valid plant IDs provided']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong']);
        }
    } catch (\Exception $e) {
        // Log the error or return the error message for debugging
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}


     public function set_statuss(Request $request)
     {
        try {
            if ($request->has('plant_ids')) {
                // Ensure the plant_ids is an array, converting it if it's a comma-separated string
                $ids = $request->input('plant_ids');
    
                if (is_string($ids)) {
                    $ids = explode(',', $ids);  // Convert comma-separated string to array
                }
    
                // Check if the array is not empty
                if (!empty($ids)) {
                    // Get the new status from the request
                    $status = $request->input('status');
    
                    // Prepare the update data
                    $updateData = ['status' => $status];
    
                    // Check if the status is 1 and set the is_approved column to 'Y'
                    if ($status == 1) {
                        $updateData['is_approved'] = 'Y';
                    }
                    // Check if the status is 0 and set the is_approved column to 'N'
                    elseif ($status == 0) {
                        $updateData['is_approved'] = 'N';
                    }
    
                    // Use the Plant model to update the status and is_approved column for all plants whose ID is in the array
                    Plant::whereIn('id', $ids)->update($updateData);
                    $plants = Plant::whereIn('id', $ids)->get();
                    foreach ($plants as $plant) {
                        $subject = $status == 1 ? 'ShowSearch - Plant Details Approved' : 'ShowSearch - Plant Details Unapproved';  // Set dynamic subject based on status
                        $plantEmail = $plant->email;  // Assuming 'email' is a field in the plant table

                        try {
                            // Send mail to the plant's email
                            Mail::to($plantEmail)->send(new ApprovedPlantMail($status, $subject, $plant));
                        } catch (\Throwable $th) {
                        }
                    }
                    return response()->json(['success' => true, 'message' => 'Request status changed successfully for all selected plants']);
                } else {
                    return response()->json(['success' => false, 'message' => 'No valid plant IDs provided']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Something went wrong']);
            }
        } catch (\Exception $e) {
            // Log the error or return the error message for debugging
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function set_statuss_all(Request $request)
    {
        if ($request->has('request_id')) {
            $ids = $request->input('request_id');
            $plant = PlantLogin::find($ids);
            $plants = Plant::where('manufacturer_id',$ids)->get();
            if ($plant && $plants->isNotEmpty()) {
                // Update the status of the PlantLogin (manufacturer)
                $plant->status = $request->input('status');
                $plant->save();  // Save the updated manufacturer status
    
                // Loop through each plant associated with this manufacturer and update the status
                foreach ($plants as $singlePlant) {
                    $singlePlant->status = $request->input('status');
                    $singlePlant->save();  // Save each plant's updated status
                }
    
                return response()->json(['success' => true, 'message' => 'Request status changed successfully for all plants']);
            } else {
                return response()->json(['success' => false, 'message' => 'Plant or manufacturer not found']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong']);
        }
    }
}
