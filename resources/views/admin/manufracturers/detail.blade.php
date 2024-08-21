@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/manufacturers.css') }}">
    <style>
       .password-wrapper {
    position: relative;
}

.password-wrapper .form-control {
    padding-right: 40px; /* Adjust padding to make space for the icon */
}

.password-wrapper .toggle-password {
    position: absolute;
    top: 50%;
    right: 10px; /* Adjust position to fit your design */
    transform: translateY(-50%);
    cursor: pointer;
    z-index: 2; /* Ensure the icon is above the input field */
}
    </style>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-heading-section">
            <h2>Plant Details</h2>
        </div>
        <div class="listed-plants-section">
            <div class="plants-details-head" style="justify-content:end;">
                <div class="plants-details-action">
                    @if ($mfs->status == 1)
                        <a class="edit-btn" style="background: var(--red);cursor:pointer !important;font-size: 14px;" data-bs-toggle="modal"
                            data-bs-target="#inactivePlant" data-plant-id="{{ $mfs['id'] }}"> Mark As Inactive</a>
                    @else
                        <a class="" data-bs-toggle="modal" data-bs-target="#activePlant"
                            data-plant-id="{{ $mfs['id'] }}"
                            style="background: var(--green);color: var(--white);padding: 12px 20px;border-radius: 5px;font-size: 14px;box-shadow: 0 4px 10px #5f0f5845;display: inline-block;position: relative;cursor:pointer">Mark
                            As Active</a>
                    @endif
                    <a class="ChangePasswordbtn" data-bs-toggle="modal" data-bs-target="#ChangePassword" style="background: var(--green);color: var(--white);padding: 12px 20px;border-radius: 5px;font-size: 14px;box-shadow: 0 4px 10px #5f0f5845;display: inline-block;position: relative;cursor:pointer" data-plant-id="{{ $mfs['id'] }}">Reset Password</a>
                </div>
            </div>
            <div class="user-table-item">
                <div class="row g-1 align-items-center">
                    <div class="col-md-4">
                        <div class="user-profile-item">
                            <div class="user-profile-media">
                                <img src="{{ asset('images/defaultuser.png') }}">
                            </div>
                            @php 
                            $type = 'N/A';
                            if($mfs->plant_type === 'corp_rep'){
                                $type =  'Corp. Representative';
                            }elseif($mfs->plant_type === 'plant_rep'){
                                $type = ' Plant Representative';
                            }
                            @endphp
                            <div class="user-profile-text">
                                <h2>{{ $mfs->plant_name ?? 'N/A' }}({{ $type ?? 'N/A' }})</h2>
                                <div class="status-text {{ $mfs->status == 1 ? 'status-active' : 'status-inactive' }}">
                                    {{ $mfs->status == 1 ? 'Active' : 'Inactive' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-1 align-items-center">
                            <div class="col-md-4">
                                <div class="user-contact-info">
                                    <div class="user-contact-info-icon">
                                        <img src="{{ asset('admin/images/location.svg') }}">
                                    </div>
                                    <div class="user-contact-info-content">
                                        <h2>Location</h2>
                                        <p>{{ $mfs->full_address ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="user-contact-info">
                                    <div class="user-contact-info-icon">
                                        <img src="{{ asset('admin/images/sms.svg') }}">
                                    </div>
                                    <div class="user-contact-info-content">
                                        <h2>Email</h2>
                                        <p>{{ $mfs->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="user-contact-info">
                                    <div class="user-contact-info-icon">
                                        <img src="{{ asset('admin/images/call.svg') }}">
                                    </div>
                                    <div class="user-contact-info-content">
                                        <h2>Phone</h2>
                                        <p>{{ $mfs->phone ? '+1 ' . $mfs->phone : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="listed-plants-section">
                <div class="plants-details-head">
                    <div class="plants-details-item">
                        <div class="plants-details-logo">
                            @if ($images->isEmpty())
                                <img src="{{ asset('images/defaultuser.png') }}">
                            @else
                                @foreach ($images as $image)
                                    <img src="{{ asset('upload/manufacturer-image/' . $image['image_url']) }}">
                                @endforeach
                            @endif
                        </div>
                        <div class="plants-details-head-text">

                        <h4>{{ $plant['plant_name'] ?? $mfs['plant_name'] ?? 'N/A' }}</h4>
                            <div class="plants-details-location">
                                <img src="{{ asset('images/location-icon.svg') }}">{{ $plant['full_address'] ?? $mfs['full_address'] }}
                            </div>
                        </div>
                    </div>


                </div>
                <div class="listed-plants-details-section">
                    <div id="plants-slider" class="owl-carousel owl-theme">
                        @foreach ($images as $image)
                            <div class="item">
                                <div class="listed-plants-slider-media">
                                    <a href="{{ asset('upload/manufacturer-image/' . $image['image_url']) }}"
                                        data-fancybox="gallery">
                                        <img src="{{ asset('upload/manufacturer-image/' . $image['image_url']) }}" />
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if (!empty($plant['description']))
                    <div class="plants-about-section">
                        <h2>About Us</h2>
                        <p>{!! nl2br(e($plant['description'])) !!}</p>
                    </div>
                @else
                    <div class="plants-about-section">
                        <h2>About Us</h2>
                        <p>N/A</p>
                    </div>
                @endif

                <div class="amenities-section">
                    <h4 style="color: var(--pink);">Specifications</h4>
                    <div class="row">
                    @if ($specifications->isNotEmpty())

                            @foreach ($specifications as $specification)
                                <div class="col-md-2">
                                    <div class="plants-amenities-info"
                                        style="position: relative;width: 100%;border-radius: 0;padding: 0;display: flex;gap: 10px;margin-bottom: 0rem;">

                                        <div class="plants-amenities-info-content"
                                            style="display: flex;gap: 10px;align-items: center;border: 1px solid;padding: 8px;width: 100%;border-radius: 5px;background: #fff; height: 50px;">
                                            @if (!empty($specification->image))
                                                <div class="plants-amenities-info-icon">
                                                    <img
                                                        src="{{ asset('upload/specification-image/' . $specification->image) }}">
                                                </div>
                                            @endif
                                            <h2 style="margin-top: 4px;border-right: 3px solid black;padding-right: 10px;">
                                                {{ $specification->name }}</h2>
                                            <p style="margin-top: 3px;">{{ $specification->values }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-2">
                                <div class="plants-amenities-info">
                                    <div class="plants-amenities-info-content">
                                        <p>N/A</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-2">
                            @if (!empty($plant['type']))
                                <div class="plants-amenities-info"
                                    style="position: relative;width: 100%;border-radius: 0;padding: 0;display: flex;gap: 10px;margin-bottom: 0rem;">
                                    <div class="plants-amenities-info-content"
                                        style="display: flex;gap: 10px;align-items: center;border: 1px solid;padding: 8px;width: 100%;border-radius: 5px;background: #fff; height:50px">

                                        <h2 style="margin-top: 4px;border-right: 3px solid black;padding-right: 10px;">Type</h2>
                                        @if ($plant['type'] == 'sw')
                                            <p style="margin-top: 3px;">Single Wide</p>
                                        @elseif ($plant['type'] == 'dw')
                                            <p style="margin-top: 3px;">Double Wide</p>
                                        @else
                                            <p>Single Wide & Double Wide</p>
                                        @endif

                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>


                </div>

                <div class="pricing-host-section">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pricing-host-box">
                                <div class="pricing-info">
                                    <div class="plants-pricing-item">
                                    @if(isset($plant['from_price_range']) && isset($plant['to_price_range']))
                                        <h2>Price Range : ${{ $plant['from_price_range'] }} - ${{ $plant['to_price_range'] }}</h2>
                                    @else
                                        <h2>Price Range : N/A</h2>
                                    @endif
                                    
                                    </div>
                                    @php
                                    if (!empty($plant) && isset($plant['type'])) {
                                        $shippingCost = DB::table('shipping_cost')
                                            ->where('type', $plant['type'])
                                            ->first();
                                    } else {
                                        $shippingCost = null; 
                                    }
                                @endphp
                                @if ($shippingCost)
                                    <div class="plants-pricing-item">
                                        <h2>Shipping Cost Per Mile @ ${{ $shippingCost->shipping_cost }}</h2>
                                    </div>
                                @endif
                                </div>
                                <div class="contact-item-info">
                                    <img
                                        src="{{ asset('images/call.svg') }}">{{ $plant && $plant['phone'] ? '+1' . $plant['phone'] : ($mfs['phone'] ? '+1' . $mfs['phone'] : 'N/A') }}
                                </div>

                                <div class="contact-item-info">
                                    <img src="{{ asset('images/sms.svg') }}"> {{ $mfs['email'] ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                        @if ($sales_managers->isNotEmpty())
                        <div class="col-md-12">
                            <div class="sales-manager-info">
                                <h1>Our Team Members</h1>
                                <div class="row">
                                    @foreach ($sales_managers as $manager)
                                        <div class="col-md-4">
                                            <div class="sales-manager-card">
                                                <div class="sales-manager-image">
                                                    @if (!empty($manager['image']))
                                                        <img
                                                            src="{{ asset('upload/sales-manager-images/' . $manager['image']) }}">
                                                    @else
                                                        <img src="{{ asset('images/profile.png') }}">
                                                    @endif
                                                </div>
                                                <div class="sales-manager-content">
                                                    <h3>{{ $manager['name'] }}</h3>
                                                    <h4>{{ $manager['designation'] }}</h4>
                                                    <div class="sales-manager-contact">
                                                        <img src="{{ asset('images/call.svg') }}">
                                                        @if(!empty($manager['phone']))
                                                            +1{{ $manager['phone'] }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </div>
                                                    <div class="sales-manager-contact">
                                                        <img src="{{ asset('images/sms.svg') }}"> {{ $manager['email'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if (empty($sales_managers))
                                        <p>No team members available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        @if ($contact_m->isNotEmpty())
                        <div class="col-md-12">
                            <div class="sales-manager-info">
                                <h1>Contacted Community Owners ({{ count($contact_m) }})</h1>
                                <div class="row">
                                @foreach ($contact_m as $item)
                                    <div class="col-md-4">
                                        <div class="sales-manager-card">
                                            <div class="sales-manager-content">
                                                <h3>{{ $item->business_name }}</h3>
                                                <h4>{{ $item->business_address ?? 'N/A' }}</h4>
                                                <div class="sales-manager-contact">
                                                    <img src="{{ asset('images/call.svg') }}">
                                                    {{ $item->mobile ?? 'N/A' }}
                                                </div>
                                                <div class="sales-manager-contact">
                                                    <img src="{{ asset('images/sms.svg') }}"> {{ $item->email ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal ss-modal fade" id="activePlant" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="ss-modal-delete">
                            <p id="delete-message">Are you sure you want to activate this Plant?</p>
                            <form id="activate-form" method="POST">
                                @csrf
                                <input type="hidden" id="plant-id" name="plant_id">
                                <div class="ss-modal-delete-action">
                                    <button type="button" id="confirm-activate" class="yes-btn">Yes, Confirm</button>
                                    <button type="button" class="cancel-btn" data-bs-dismiss="modal"
                                        aria-label="Close">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal ss-modal fade" id="inactivePlant" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="ss-modal-delete">
                            <p id="delete-message">Are you sure you want to In-activate this Plant?</p>
                            <form id="inactivate-form" method="POST">
                                @csrf
                                <input type="hidden" id="plant-id" name="plant_id">
                                <div class="ss-modal-delete-action">
                                    <button type="button" id="confirm-inactivate" class="yes-btn">Yes, Confirm</button>
                                    <button type="button" class="cancel-btn" data-bs-dismiss="modal"
                                        aria-label="Close">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal lm-modal fade" id="ChangePassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ss-modal-form">
                    <h2>Reset Password</h2>
                    <form id="changePasswordForm">
                        @csrf
                        <input type="hidden" id="plant-id" name="plant_id">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="password-wrapper">
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Create New Password" required>
                                        <span toggle="#password" class="eye-toggle fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="password-wrapper">
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm New Password" required>
                                        <span toggle="#password_confirmation" class="eye-toggle fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="save-btn mb-2">Reset Password</button>
                                    <button type="button" class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="changePasswordMessage"></div>
                </div>
            </div>
        </div>
    </div>
</div>

        <script>
            $(document).ready(function() {
                // Set plant ID in hidden input field when the modal is triggered
                $('[data-bs-toggle="modal"]').on('click', function() {
                    var plantId = $(this).data('plant-id');
                    console.log(plantId);
                    $('#plant-id').val(plantId);
                });

                // Handle form submission via AJAX
                $('#confirm-activate').on('click', function() {
                    var plantId = $('#plant-id').val();
                    $.ajax({
                        url: '{{ route('admin.activate.plant') }}', // Adjust the URL to match your route
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            plant_id: plantId
                        },
                        success: function(response) {
                            // Close the modal
                            $('#activePlant').modal('hide');
                            // Reload the page
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('An error occurred while activating the plant.');
                        }
                    });
                });


                $('#confirm-inactivate').on('click', function() {
                    var plantId = $('#plant-id').val();
                    $.ajax({
                        url: '{{ route('admin.inactivate.plant') }}', // Adjust the URL to match your route
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            plant_id: plantId
                        },
                        success: function(response) {
                            // Close the modal
                            $('#inactivePlant').modal('hide');
                            // Reload the page
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('An error occurred while activating the plant.');
                        }
                    });
                });
            });
        </script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        
        var form = e.target;
        var formData = new FormData(form);
        var manufacturerId = document.getElementById('plant-id').value;
        const url = `{{ route("admin.updatePassword", ["id" => ":id"]) }}`.replace(':id', manufacturerId);


        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                _token: '{{ csrf_token() }}',
                 'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            var messageContainer = document.getElementById('changePasswordMessage');
            let modal = new bootstrap.Modal(document.getElementById('ChangePassword'));

            if (data.errors) {
                messageContainer.innerHTML = '<div class="alert alert-danger">' + data.errors.join('<br>') + '</div>';
            } else {
                messageContainer.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                form.reset(); // Optionally reset the form
                
                // Hide the modal after displaying the success message
                setTimeout(() => {
                    modal.hide();
                    window.location.reload();
                }, 2000); // Adjust the delay as needed
            }
        })
        .catch(error => {
            console.error('Error:', error);
            var messageContainer = document.getElementById('changePasswordMessage');
            let modal = new bootstrap.Modal(document.getElementById('ChangePassword'));

            messageContainer.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';

            // Hide the modal after displaying the error message
            setTimeout(() => {
                modal.hide();
            }, 2000); // Adjust the delay as needed
        });
    });
});


document.querySelectorAll('.toggle-password').forEach(function(icon) {
        icon.addEventListener('click', function() {
            var input = document.querySelector(icon.getAttribute('toggle'));
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
    @endsection
