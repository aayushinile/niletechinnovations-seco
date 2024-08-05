@extends('manufacturer.layouts')
<style>
    .add-plants-action a.addmoremanager {
        background: var(--pink);
        color: var(--white);
        padding: 10px 15px;
        border-radius: 5px;
        font-size: 14px;
        box-shadow: 0 4px 10px #5f0f5845;
        display: inline-block;
        position: relative;
    }

    .mapboxgl-ctrl-geocoder--input {
        background: #eee2ed !important;
        border-radius: 5px !important;
        font-size: 13px !important;
        border: 1px solid var(--border) !important;
        font-weight: 400 !important;
        height: auto !important;
        padding: 0.94rem 0.94rem !important;
        outline: 0 !important;
        width: 100%;
        display: inline-block !important;
        color: var(--pink) !important;
        box-shadow: 0px 8px 13px 0px rgba(0, 0, 0, 0.05) !important;
    }

    .mapboxgl-ctrl-geocoder--icon {
        display: none !important;
    }

    @media screen and (min-width: 640px) {
        .mapboxgl-ctrl-geocoder {
            width: auto !important;
            /* Overrides the width property */
            max-width: none !important;
            /* Overrides the max-width property */
        }
    }

    .loader-container {
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Semi-transparent black overlay */
        display: none;
        /* Initially hidden */
        justify-content: center;
        align-items: center;
    }

    .loader {
        border: 8px solid #f3f3f3;
        /* Light grey */
        border-top: 8px solid #3498db;
        /* Blue */
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        position: relative;
        top: 46%;
        left: 46%;


    }

    .loader-container.show {
        display: flex;
    }


    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    span.input-group-text {
        background: transparent;
        border-radius: 5px;
        font-size: 13px;
        border: 1px solid var(--border);
        font-weight: 400;
        height: auto;
        padding: 10px;
        outline: 0;
        display: inline-block;
        color: var(--pink);
        box-shadow: 0px 8px 13px 0px rgba(0, 0, 0, 0.05);
    }

    .error-message {
        color: red;
        font-size: 14px;
        margin-top: 5px;
    }

    .img-fluid {
        height: 200px !important;
    }

    .upload-file-item-icon.visible {
        display: block !important;
    }
    .Uploadphoto-thumb{
        width: unset !important;
    }
</style>
@section('content')
    <div class="body-main-content">
        <div class="ss-heading-section">
            <h2>Manage Plant Details</h2>
            <div class="search-filter wd20">
                <div class="row g-1  justify-content-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <a href="{{ route('manufacturer.manage-locations') }} "class="addnewplant-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form id="add-plant-form" action="{{ route('savePlant') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @php
                $plant = Auth::user();
            @endphp
            <div class="add-plants-section">
                <div class="add-plants-item">
                    <h2>Add New Plant</h2>

                    <div class="add-plants-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h5>Plant Name *</h5>
                                    <input type="text" class="form-control" name="plant_name" placeholder="Plant Name"
                                        value="{{ $plant['plant_name'] }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <h5>Email *</h5>
                                    <input type="email" class="form-control email" name="email"
                                        placeholder="Email Address" value="{{ $plant['email'] }}">
                                    <span id="emailError" class="error-message"></span>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <h5>Phone</h5>
                                    <div class="form-group-phone">
                                        <span class="input-group-text">+1</span>
                                        <div class="input-group-form-control">
                                            <input type="text" class="form-control phone" name="phone"
                                                placeholder="Phone Number" maxlength="10" value="{{ $plant['phone'] }}">
                                            <div class="invalid-feedback">Please enter a 10-digit phone number.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <h5>Location *</h5>

                                    <input id="geocoder" class="form-control" type="text"
                                        placeholder="Plant Full Address" required value="{{ $plant['full_address'] }}">
                                    <input type="hidden" id="full_address" name="full_address" required
                                        class="form-control" value="{{ $plant['full_address'] }}">
                                    <input type="hidden" id="latitude" name="latitude" value="{{ $plant['latitude'] }}">
                                    <input type="hidden" id="longitude" name="longitude" value="{{ $plant['longitude'] }}">
                                    <input type="hidden" id="locationSelected" name="locationSelected" value="false">
                                    <!-- <span class="form-input-icon"><img src="{{ asset('images/location.svg') }}"></span> -->
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                                                                                                                                                                                                    <div class="form-group">
                                                                                                                                                                                                                        <h5>State </h5>
                                                                                                                                                                                                                        <select class="form-control">
                                                                                                                                                                                                                            <option>Choose State</option>
                                                                                                                                                                                                                        </select>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div> -->
                            <!-- <div class="col-md-3">
                                                                                                                                                                                                                    <div class="form-group">
                                                                                                                                                                                                                        <h5>City </h5>
                                                                                                                                                                                                                        <input type="text" class="form-control" name="city" placeholder="Plant Name">
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div> -->
                            <!-- <div class="col-md-3">
                                                                                                                                                                                                                    <div class="form-group">
                                                                                                                                                                                                                        <h5>Zip </h5>
                                                                                                                                                                                                                        <input type="text" class="form-control" name="zipcode" placeholder="Plant Name">
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div> -->

                            <div class="col-md-12">
                                <div class="form-group">
                                    <h5>Description *</h5>
                                    <textarea class="form-control" placeholder="Description" name="description"></textarea>
                                    @error('description')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <h5>Type *</h5>
                                    <select class="form-control" name="type">
                                        <option value="sw">Single Wide</option>
                                        <option value="dw">Double Wide</option>
                                        <option value="sw_dw">Single Wide & Double Wide</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group form-group-icon">
                                    <h5>From Price Range *</h5>
                                    <input type="number" name="from_price_range" required class="form-control"
                                            placeholder="$0.00" >
                                    <span class="form-input-icon"><img
                                            src="{{ asset('images/dollar-circle.png') }}"></span>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group form-group-icon">
                                    <h5>To Price Range *</h5>
                                    <input type="number" name="to_price_range" required="" class="form-control"
                                        placeholder="$0. 00">
                                    <span class="form-input-icon"><img
                                            src="{{ asset('images/dollar-circle.png') }}"></span>
                                </div>
                            </div>
                            <!-- <div class="col-md-4">
                                                                                                                                                                                                                    <div class="form-group form-group-icon">
                                                                                                                                                                                                                        <h5>Shipping Cost (Rate Per Miles) *</h5>
                                                                                                                                                                                                                        <input type="text" name="shipping_cost" required="" class="form-control" placeholder="$0. 00">
                                                                                                                                                                                                                        <span class="form-input-icon"><img src="{{ asset('images/dollar-circle.png') }}"></span>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div> -->

                            <div class="col-md-12">
                                <div class="form-group">
                                    <h5>Upload Photos</h5>
                                    <div class="Uploadphoto-file">
                                        <input type="file" multiple="multiple" id="UploadPhotos"
                                            class="ssUploadphoto" name="images[]" onchange="previewImages(event)"
                                            accept=".png, .jpg, .jpeg">
                                        <label for="UploadPhotos">
                                            <div class="Uploadphoto-text">
                                                <div class="exportfile-text"><span><img
                                                            src="{{ asset('images/upload-icon.svg') }}"> Upload
                                                        Photos</span></div>
                                                <div class="ChooseFile-text">Choose File</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="preview_container" class="row g-1 mt-2"></div>
                            <!-- <div class="upload-file-action">
                                                                                                                                                                                                            <a class="delete-btn" href="#"><img src="{{ asset('images/close-circle.svg') }}"></a>
                                                                                                                                                                                                                                        </div> -->

                            <!-- <div class="col-md-4">
                                                                                                                                                                                                                    <div class="specification-info">
                                                                                                                                                                                                                        <div class="specification-content">
                                                                                                                                                                                                                            <div class="specification-info-icon">
                                                                                                                                                                                                                                <img src="{{ asset('images/1.jpg') }}">
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                            <div class="specification-info-content">
                                                                                                                                                                                                                                <h2>Untitled.jpg</h2>
                                                                                                                                                                                                                                <p>2KB</p>
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                        <div class="specification-action"><a href="#">Remove</a></div>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div> -->

                        </div>
                    </div>
                </div>
                <div id="add-plants-container">
                    <div class="add-plants-item">
                        <div class="sales-add-card">
                            <div class="sales-add-head">
                                <h2>Team Member Details</h2>
                                <div class="add-plants-action">
                                    <a class="addmoremanager" href="javascript:void(0)" style="cursor: pointer;">Add
                                        More</a>
                                </div>
                            </div>
                            <div class="sales-add-body ">
                                <div class="sales-add-form">
                                    <div class="row g-1">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h5>Name</h5>
                                                <input type="text" class="form-control text-capitalize" name="sales_manager[name][]"
                                                    placeholder="Name">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h5>Designation </h5>
                                                <input type="text" class="form-control"
                                                    name="sales_manager[designation][]" placeholder="Designation">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h5>Email</h5>
                                                <input type="email" class="form-control" name="sales_manager[email][]"
                                                    placeholder="Email Address">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h5>Phone </h5>
                                                <div class="form-group-phone">
                                                    <span class="input-group-text">+1</span>
                                                    <div class="input-group-form-control">
                                                        <input type="text" class="form-control phone"
                                                            name="sales_manager[phone][]" placeholder="Phone Number"
                                                            maxlength="10">
                                                        <div class="invalid-feedback">Please enter a 10-digit phone number.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h5>Upload Photos </h5>
                                                <div class="salesUploadphoto">
                                                    <div class="Uploadphoto-file1">
                                                        <input type="file" multiple="multiple" id="Upload Photos"
                                                            class="ssUploadphoto1" name="sales_manager[images][]"
                                                            onchange="previewImage(event)">
                                                        <label for="Upload Photos">
                                                            <div class="Uploadphoto-text">
                                                                <div class="exportfile-text"><img
                                                                        src="{{ asset('images/upload-icon.svg') }}"
                                                                        height="24"></div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    <div class="Uploadphoto-thumb">
                                                        <img id="imagePreview" class="">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group">
                                                                                                                                                                                                                                <h5>Upload Photos </h5>
                                                                                                                                                                                                                                <input type="file" class="form-control"  name=""  multiple="multiple" />
                                                                                                                                                                                                                            </div> -->
                                        </div>

                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <button class="btn-remove">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="add-plants-item">
                    <div class="sales-add-card">
                        <div class="sales-add-head">
                            <h2>Specification *</h2>
                            <div class="add-plants-action">
                                <a class="addmore" data-bs-toggle="modal" onclick="$('.upload-file-item').hide()"
                                    data-bs-target="#addspecification" style="cursor: pointer;">Add More</a>
                            </div>
                        </div>
                        <div class="sales-add-body">
                            <div class="sales-add-form">

                                <div class="row g-1" id="specifications-container">
                                    @if ($specifications->isNotEmpty())
                                        @foreach ($specifications as $specification)
                                            <div class="col-md-4" id="specification-{{ $specification->id }}">
                                                <div class="specification-info">
                                                    <div class="specification-content">
                                                        <div class="specificationcheckbox">
                                                            <input type="checkbox" name="specifications[]"
                                                                id="{{ $specification->id }}"
                                                                value="{{ $specification->id }}">
                                                            <label for="{{ $specification->id }}">&nbsp</label>
                                                        </div>
                                                        @if (!empty($specification->image))
                                                            <div class="specification-info-icon">
                                                                <img
                                                                    src="{{ asset('upload/specification-image/' . $specification->image) }}">
                                                            </div>
                                                        @endif
                                                        <div class="specification-info-content">
                                                            <h2>{{ $specification->name }}</h2>
                                                            <p>{{ $specification->values }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="specification-action">
                                                        <a class="editbtn1" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#editspecification"
                                                            onclick="openEditForm(this)"
                                                            data-id="{{ $specification->id }}"
                                                            data-name="{{ $specification->name }}"
                                                            data-values="{{ $specification->values }}"
                                                            data-image = "{{ $specification->image }}"><img
                                                                src="{{ asset('images/edit-2.svg') }}"
                                                                style="margin-top:6px"></a>
                                                        <a class="trashbtn remove-specification" style="cursor: pointer"
                                                            onclick="deleteSpec({{ $specification->id }})"
                                                            data-specification-id="{{ $specification->id }}"><img
                                                                src="{{ asset('images/trash.svg') }}"
                                                                style="margin-top:6px"></a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                @if (!$specifications->isNotEmpty())
                                    <div class="row g-1" id="emptySpecifications">
                                        <div class="col-md-12">
                                            <div class=" justify-content-center">
                                                <div class="specification-content justify-content-center">
                                                    <div class="specification-info-content">
                                                        <p>No specification added yet.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="add-plants-foot">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a href="{{ route('manufacturer.manage-locations') }} "class="addnewplant-btn"
                                    style="width: 10%;padding: 10px 15px;">Cancel</a>
                                <button type="submit" class="savecreatebtn">Add & Create</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="loader-container" id="loader">
                <div class="loader"></div>
            </div>
        </form>
    </div>


    <!-- add specification -->

    <div class="modal ss-modal fade" id="addspecification" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-form">
                        <h2>Add Specification</h2>
                        <form id="specificationForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text" name="name" required="" class="form-control"
                                            placeholder="Name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text" name="values" required="" class="form-control"
                                            placeholder="Value">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="file" id="spec_icon" name="image" class="form-control"
                                            placeholder="Business Logo" onchange="previewSpecImage(event)">
                                    </div>

                                    <div class="upload-file-item" style="display: none;">
                                        <div class="upload-file-item-content">
                                            <div class="upload-file-media">
                                                <img id="preview_image" src="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="save-btn">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal ss-modal fade" id="editspecification" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-form">
                        <h2>Edit Specification</h2>
                        <form id="specificationEditForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="spec-id">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text" name="name" required="" class="form-control"
                                            placeholder="Name" id="spec-name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text" name="values" required="" class="form-control"
                                            placeholder="Value" id="spec-values">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="file" id="spec_icon" name="image" class="form-control"
                                        placeholder="Business Logo" onchange="previewSpecImage2(event)">
                                    <div class="upload-file-item-icon" style="display: none;">
                                        <div class="upload-file-item-content">
                                            <div class="upload-file-media">
                                                <img id="preview_image_icon" src="">
                                            </div>
                                        </div>
                                        <div class="upload-file-action">
                                            <a class="delete-btn-icon" href="#"><img
                                                    src="{{ asset('images/close-circle.svg') }}"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="save-btn">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Successfully plants -->
    <div class="modal ss-modal fade" id="Successfullyplants" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-delete">
                        <div class="ss-modal-delete-icon" style="background: #fafcf9;"><img
                                src="{{ asset('images/tick-circle1.svg') }}"></div>
                        <p>Plant Created Successfully</p>
                        <div class="ss-modal-delete-action">
                            <button class="yes-btn" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal ss-modal fade" id="Successfullyspecifications" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-delete">
                        <div class="ss-modal-delete-icon" style="background: #fafcf9;"><img
                                src="{{ asset('images/tick-circle1.svg') }}"></div>
                        <p>Specification Added Successfully</p>
                        <div class="ss-modal-delete-action">
                            <button class="yes-btn" id="closeModalBtn">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal ss-modal fade" id="Deletespecifications" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-delete">
                        <div class="ss-modal-delete-icon" style="background: #fafcf9;"><img
                                src="{{ asset('images/tick-circle1.svg') }}"></div>
                        <p>Specification Removed Successfully</p>
                        <div class="ss-modal-delete-action">
                            <button class="yes-btn" id="closeModaldeleteBtn">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal ss-modal fade" id="EditSucesspecifications" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-delete">
                        <div class="ss-modal-delete-icon" style="background: #fafcf9;"><img
                                src="{{ asset('images/tick-circle1.svg') }}"></div>
                        <p>Specification Updated Successfully</p>
                        <div class="ss-modal-delete-action">
                            <button class="yes-btn" id="closeModaleditBtn">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#closeModalBtn').on('click', function() {
                $('#Successfullyspecifications').modal('hide');
            });


            $('#closeModaldeleteBtn').on('click', function() {
                $('#Deletespecifications').modal('hide');
            });



        });
    </script>
    <script>
        $(document).ready(function() {
            // Use event delegation to handle dynamically added elements
            $(document).on('input', '.phone', function() {
                var phoneNumber = $(this).val().replace(/\D/g, ''); // Remove non-numeric characters
                var formattedPhoneNumber = formatPhoneNumber(phoneNumber); // Format the phone number

                // Update the input value with formatted phone number
                $(this).val(formattedPhoneNumber);

                // Validate the formatted phone number
                validatePhoneNumber(formattedPhoneNumber, $(this));
            });

            function formatPhoneNumber(phoneNumber) {
                // Apply the phone number format (999)-999-9999
                var formattedPhoneNumber = phoneNumber.replace(/(\d{3})(\d{3})(\d{4})/, '($1)-$2-$3');
                return formattedPhoneNumber;
            }

            function validatePhoneNumber(phoneNumber, element) {
                // Check if the formatted phone number has exactly 10 digits
                var isValid = /^\(\d{3}\)-\d{3}-\d{4}$/.test(phoneNumber);

                // Toggle 'is-invalid' class based on validation
                if (!isValid) {
                    element.addClass('is-invalid');
                } else {
                    element.removeClass('is-invalid');
                }
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            // Validate email on input change
            $(document).on('input', '.email', function() {
                validateEmailOnInput();
            });

            // Function to validate email on input change
            function validateEmailOnInput() {
                var email = $('.email').val().trim();
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var emailError = $('#emailError');

                if (!emailPattern.test(email)) {
                    emailError.text('Please enter a valid email address');
                } else {
                    emailError.text('');
                }
            }

            // Function to validate email on form submission
            function validateEmail() {
                var email = $('.email').val().trim();
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var emailError = $('#emailError');

                if (!emailPattern.test(email)) {
                    emailError.text('Please enter a valid email address');
                    return false;
                } else {
                    emailError.text('');
                    return true;
                }
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addMoreButton = document.querySelector('.addmoremanager');
            const container = document.getElementById('add-plants-container');

            function createNewRow() {
                const newRow = document.createElement('div');
                newRow.classList.add('sales-add-form');

                // Generate a unique ID for each new row
                const uniqueId = `row-${Date.now()}`;

                newRow.innerHTML = `
                    <div class="row g-1">
                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Name</h5>
                                <input type="text" class="form-control text-capitalize" name="sales_manager[name][]" placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Designation</h5>
                                <input type="text" class="form-control" name="sales_manager[designation][]" placeholder="Designation">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Email</h5>
                                <input type="email" class="form-control" name="sales_manager[email][]" placeholder="Email Address">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h5>Phone</h5>
                                <div class="form-group-phone">
                                    <span class="input-group-text">+1</span>
                                    <div class="input-group-form-control">
                                        <input type="text" class="form-control phone" name="sales_manager[phone][]" placeholder="Phone Number" maxlength="10">
                                        <div class="invalid-feedback">Please enter a 10-digit phone number.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h5>Upload Photos</h5>
                                <div class="salesUploadphoto">
                                    <div class="Uploadphoto-file1">
                                        <input type="file" multiple="multiple" id="uploadPhotos-${uniqueId}" class="ssUploadphoto1" name="sales_manager[images][]" onchange="previewImages2(event, 'previewImage-${uniqueId}')">
                                        <label for="uploadPhotos-${uniqueId}">
                                            <div class="Uploadphoto-text">
                                                <div class="exportfile-text"><img src="{{ asset('images/upload-icon.svg') }}" height="24"></div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="Uploadphoto-thumb">
                                        <img class="preview-image" id="previewImage-${uniqueId}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <button class="btn-remove">Remove</button>
                            </div>
                        </div>
                    </div>
                `;
                newRow.querySelector('.btn-remove').addEventListener('click', function() {
                    newRow.remove();
                });
                return newRow;
            }

            addMoreButton.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default link behavior
                const newRow = createNewRow();
                container.querySelector('.add-plants-item').appendChild(newRow);
            });

            container.querySelector('.btn-remove').addEventListener('click', function() {
                container.querySelector('.sales-add-body').remove();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#specificationForm').on('submit', function(e) {
                document.getElementById('loader').style.display = 'block';
                e.preventDefault();

                var formData = new FormData(this);
                var baseUrl = "{{ url('/') }}";
                console.log(baseUrl);
                var editIconUrl = "{{ asset('images/edit-2.svg') }}";
                var trashIconUrl = "{{ asset('images/trash.svg') }}";

                $.ajax({
                    url: baseUrl + '/save-specification', // Your backend endpoint
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            console.log(response);
                            $('#Successfullyspecifications').modal('show');
                            $('#addspecification').modal('hide');
                            $('#specificationForm')[0].reset();
                            $("#spec_icon").val('');

                            // Create a new specification item dynamically
                            var newSpec = $('<div class="col-md-4">' +
                                '<div class="specification-info">' +
                                '<div class="specification-content">' +
                                '<div class="specification-info-content">' +
                                '<input type="checkbox" name="specifications[]" value="' +
                                response.specification.id + '">' +
                                '<h2>' + response.specification.name + '</h2>' +
                                '<p>' + response.specification.values + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="specification-action">' +
                                '<a class="editbtn1" href="#" data-bs-toggle="modal" data-bs-target="#editspecification" ' +
                                'data-id="' + response.specification.id + '" ' +
                                'data-name="' + response.specification.name + '" ' +
                                'data-values="' + response.specification.values + '">' +
                                '<img src="' + editIconUrl + '" style="margin-top:6px">' +
                                '</a>' +
                                '<a href="#" class="trashbtn remove-specification" data-specification-id="' +
                                response.specification.id + '">' +
                                '<img src="' + trashIconUrl + '" style="margin-top:6px">' +
                                '</a>' +
                                '</div>' +
                                '</div>' +
                                '</div>');
                            $('#emptySpecifications').remove();
                            // Append the new specification item to the container
                            // $('#specifications-container').append(newSpec);
                            $('#specifications-container').html(response.view);

                            document.getElementById('loader').style.display = 'none';

                            // setTimeout(function() {
                            //     location.reload();
                            // }, 2000);
                        } else {
                            alert('Failed to save specification: ' + response.message);
                            document.getElementById('loader').style.display = 'none';
                        }
                    },
                    error: function(xhr, status, error) {
                        document.getElementById('loader').style.display = 'none';
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.js'></script>
    <!-- Mapbox Geocoder -->
    <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js'></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=env('GOOGLE_API_KEY')&libraries=places">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('geocoder');
            var autocomplete = new google.maps.places.Autocomplete(input);

            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();

                if (!place.geometry) {
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                document.getElementById('full_address').value = place.formatted_address;
                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
                document.getElementById('locationSelected').value = 'true';
            });

            // Handle clear event
            document.getElementById('geocoder').addEventListener('change', function() {
                if (document.getElementById('geocoder').value === '') {
                    document.getElementById('full_address').value = '';
                    document.getElementById('latitude').value = '';
                    document.getElementById('longitude').value = '';
                    document.getElementById('locationSelected').value = 'false';
                }
            });

            // Form submission validation
            document.getElementById('add-plant-form').addEventListener('submit', function(e) {
                var locationSelected = document.getElementById('locationSelected').value;

                // Validation: Check if location is selected
                // if (!locationSelected == 'true') {
                //     alert('Please select a location from the dropdown.');
                //     e.preventDefault(); // Prevent form submission
                //     return; // Exit the function to avoid showing the loader
                // }

                // Show the loader if the form is valid
                document.getElementById('loader').style.display = 'block';
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // $('.remove-specification').on('click', function(e) {
            //     e.preventDefault();

            //     var specificationId = $(this).data('specification-id');
            //     var baseUrl = "{{ url('/') }}";

            //     $.ajax({
            //         url: baseUrl + '/remove-specification/' + specificationId,

            //         type: 'POST', // Using DELETE method
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         success: function(response) {
            //             $('#specification-' + specificationId).fadeOut(300, function() {
            //                 $(this).remove();
            //             });
            //             $('#specification-' + specificationId).remove();
            //             $('#Deletespecifications').modal('show');
            //             setTimeout(function() {
            //                 location.reload();
            //             }, 2000);
            //             //console.log('Specification removed successfully.');
            //         },
            //         error: function(xhr, status, error) {
            //             alert('Error deleting specification: ' + error);
            //         }
            //     });
            // });
        });
    </script>
    <script>
          let filesArray = []; // Array to keep track of files

function previewImages(event) {
    var input = event.target;
    var previewContainer = document.getElementById('preview_container');

    // Clear existing previews
    previewContainer.innerHTML = '';

    if (input.files) {
        filesArray = Array.from(input.files); // Update the filesArray

        filesArray.forEach((file, index) => {
            var reader = new FileReader();

            reader.onload = function(e) {
                var colDiv = document.createElement('div');
                colDiv.classList.add('col-md-3', 'mb-2');
                colDiv.id = 'preview-' + index; // Assign a unique id to each preview

                var imgDiv = document.createElement('div');
                imgDiv.classList.add('position-relative');

                var img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('img-fluid');

                var deleteBtn = document.createElement('button');
                deleteBtn.classList.add('btn', 'btn-danger', 'btn-sm', 'position-absolute', 'top-0', 'end-0');
                deleteBtn.innerHTML = '&times;';
                deleteBtn.onclick = function() {
                    deleteImage(index);
                };

                imgDiv.appendChild(img);
                imgDiv.appendChild(deleteBtn);
                colDiv.appendChild(imgDiv);
                previewContainer.appendChild(colDiv);
            }

            reader.readAsDataURL(file);
        });
    }
}

function deleteImage(index) {
    // Remove preview from the container
    var previewDiv = document.getElementById('preview-' + index);
    if (previewDiv) {
        previewDiv.remove();
    }

    // Remove the file from the filesArray
    filesArray.splice(index, 1);

    // Clear the file input and reassign the remaining files
    var input = document.getElementById('UploadPhotos');
    var dataTransfer = new DataTransfer();

    filesArray.forEach(file => {
        dataTransfer.items.add(file);
    });

    // Set the new file list
    input.files = dataTransfer.files;

    // Update the preview
    previewImages({ target: input });

    // Clear the file input value if there are no files left
    if (filesArray.length === 0) {
        input.value = '';
    }
}

// Optional: Clear filesArray on form reset
document.querySelector('form').addEventListener('reset', function() {
    filesArray = [];
    document.getElementById('preview_container').innerHTML = '';
    document.getElementById('UploadPhotos').value = ''; // Clear file input
});

        function previewImage(event) {
            var input = event.target;
            var previewContainer = document.querySelector('.Uploadphoto-thumb');
            var existingImage = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                var file = input.files[0];
                var reader = new FileReader();

                reader.onload = function(e) {
                    if (existingImage) {
                        // Update the existing image preview
                        existingImage.src = e.target.result;
                    } else {
                        // If no existing image, create a new image preview element
                        var newImagePreview = document.createElement('img');
                        newImagePreview.src = e.target.result;
                        newImagePreview.classList.add('imgfluid');
                        newImagePreview.id = 'imagePreview'; // Set the ID for future reference

                        // Append the new image preview to the container
                        previewContainer.appendChild(newImagePreview);
                    }
                }

                reader.readAsDataURL(file);
            }
        }


        function previewImages2(event, previewId) {
            const files = event.target.files;
            const preview = document.getElementById(previewId);
            if (files && files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(files[0]);
            }
        }


        function openEditForm(ele) {
            // Get data attributes from the button
            const name = ele.getAttribute('data-name');
            const values = ele.getAttribute('data-values');
            const id = ele.getAttribute('data-id');
            const image = ele.getAttribute('data-image');
            console.log(image);

            // Populate the modal form fields
            document.getElementById('spec-id').value = id;
            document.getElementById('spec-name').value = name;
            document.getElementById('spec-values').value = values;

            const previewImage = document.getElementById('preview_image_icon');
            const uploadFileItem = document.querySelector('.upload-file-item-icon');

            if (image) {
                previewImage.src = "{{ asset('upload/specification-image/') }}/" + image;
                uploadFileItem.classList.add(
                    'visible'); // Show the upload file item container
            } else {
                previewImage.src = '';
                uploadFileItem.style.display =
                    'none'; // Hide the upload file item container
            }

        }

        function deleteSpec(specificationId) {

            // e.preventDefault();


            var baseUrl = "{{ url('/') }}";

            $.ajax({
                url: baseUrl + '/remove-specification/' + specificationId,

                type: 'POST', // Using DELETE method
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#specification-' + specificationId).fadeOut(300, function() {
                        $(this).remove();
                    });
                    $('#specification-' + specificationId).remove();
                    $('#Deletespecifications').modal('show');
                    setTimeout(function() {
                        // location.reload();
                        $('#Deletespecifications').modal('hide');

                    }, 2000);
                    //console.log('Specification removed successfully.');
                },
                error: function(xhr, status, error) {
                    alert('Error deleting specification: ' + error);
                }
            });

        }
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.editbtn1');

            // editButtons.forEach(button => {
            //     button.addEventListener('click', function() {
            //         // Get data attributes from the button
            //         const name = this.getAttribute('data-name');
            //         const values = this.getAttribute('data-values');
            //         const id = this.getAttribute('data-id');
            //         const image = this.getAttribute('data-image');
            //         console.log(image);

            //         // Populate the modal form fields
            //         document.getElementById('spec-id').value = id;
            //         document.getElementById('spec-name').value = name;
            //         document.getElementById('spec-values').value = values;

            //         const previewImage = document.getElementById('preview_image_icon');
            //         const uploadFileItem = document.querySelector('.upload-file-item-icon');

            //         if (image) {
            //             previewImage.src = "{{ asset('upload/specification-image/') }}/" + image;
            //             uploadFileItem.classList.add(
            //                 'visible'); // Show the upload file item container
            //         } else {
            //             previewImage.src = '';
            //             uploadFileItem.style.display =
            //                 'none'; // Hide the upload file item container
            //         }
            //     });
            // });

            document.getElementById('specificationEditForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission
                document.getElementById('loader').style.display = 'block';
                const formData = new FormData(this);
                const specId = document.getElementById('spec-id').value;
                console.log(specId);

                fetch("{{ route('updateSpecification', ':id') }}".replace(':id', specId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                        },
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            $('#editspecification').modal('hide');
                            $('#EditSucesspecifications').modal('show');
                            // location.reload();
                            setTimeout(function() {
                                $('#EditSucesspecifications').modal('hide');



                            }, 2000);
                            $('#specifications-container').html(data.view);

                            document.getElementById('loader').style.display =
                                'none'; // Refresh the page to show updated data
                        } else {
                            alert('Failed to update specification.');
                            document.getElementById('loader').style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the specification.');
                    });
            });
        });
        document.getElementById('spec_icon').addEventListener('change', previewSpecImage);

        function previewSpecImage(event) {
            var input = event.target;

            var preview = document.getElementById('preview_image');

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    // Show the upload-file-item div if an image is selected
                    document.querySelector('.upload-file-item').style.display = 'block';
                    //console.log(e.target.result);
                }


                reader.readAsDataURL(input.files[0]);
                console.log(input.files[0]);
            } else {
                preview.src = "";
                // Hide the upload-file-item div if no image is selected
                document.querySelector('.upload-file-item').style.display = 'none';
            }
        }


        function previewSpecImage2(event) {
            const input = event.target;
            const preview = document.getElementById('preview_image_icon');
            console.log(preview);
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    document.querySelector('.upload-file-item').style.display =
                        'block'; // Show the new image preview container
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                document.querySelector('.upload-file-item').style.display =
                    'none'; // Hide the new image preview container if no image selected
            }
        }



        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn-icon');

            deleteButtons.forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();

                    const imageId = document.getElementById('spec-id').value;
                    const imageElement = this.closest('.upload-file-item-icon');

                    try {
                        const response = await fetch("{{ route('delete-spec-photo', ':id') }}"
                            .replace(':id', imageId), {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id: imageId
                                })
                            });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            imageElement.remove();
                            window.location.reload();
                        } else {
                            alert('Failed to delete the image.');
                        }
                    } catch {
                        alert('An error occurred while deleting the image.');
                    }
                });
            });
        });
    </script>
@endsection
