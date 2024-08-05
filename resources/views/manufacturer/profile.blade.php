@extends('manufacturer.layouts')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
    .pac-container {
        z-index: 9999; /* Ensure this is higher than the modal's z-index */
    }
    span.input-group-text {
            background: transparent;
            border-radius: 5px;
            font-size: 13px;
            border: 1px solid var(--border);
            font-weight: 400;
            height: auto;
            padding: 15px;
            outline: 0;
            display: inline-block;
            color: var(--pink);
            box-shadow: 0px 8px 13px 0px rgba(0, 0, 0, 0.05);
        }
        .field-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
            margin-right: 10px;
            margin-top: -9px;
        }

        .field-icon:hover {
            color: #000;
            margin-right: 10px;
            margin-top: -9px;
        }
</style>
@section('content')
            <div class="body-main-content">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
                <div class="ss-heading-section">
                    <h2>My Profile</h2>
                </div>
                <div class="listed-plants-section">
                    <div class="profile-info-item">
                        <div class="profile-info-item-media">
                        @if (!empty($attributes->attribute_value))
                            <a href="#"><img src="{{ asset('upload/manufacturer-image/'.$attributes->attribute_value) }}"></a>
                        @else
                            <a href="#"><img src="{{ asset('images/defaultuser.png') }}"></a>
                        @endif
                        </div>
                        <div class="profile-info-item-content">
                            <h2>{{$user->manufacturer_name ?? ''}}</h2>
                            <div class="profile-info-point">
                            @php
                                            $status = '';
                                             if($user->status == '0'){
                                                $status = 'Not Verified';
                                             }else{
                                                $status = 'Verified';
                                             }
                                            @endphp
                                            <!-- <div class="Ownertext">
                                            {{ isset($users->plant_name) && $users->plant_name != '' ? $users->plant_name : 'Admin' }}
                                        </div> -->
                                <!-- @if($status == 'Verified')
                                <div class="Verifiedtext">
                                    <img src="{{asset('images/tick.svg')}}"> {{$status}}
                                </div>
                                @else
                                <div class="Verifiedtext" style="color:var(--red)">
                                <img src="{{asset('images/not-verify.svg')}}"> {{$status}}
                                </div>
                                @endif -->
                                
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="User-contact-info">
                                        <div class="User-contact-info-icon">
                                            <img src="{{ asset('images/sms.svg') }}">
                                        </div>
                                        <div class="User-contact-info-content">
                                            <h2>Email Address</h2>
                                            <p>{{$users->email ?? 'N/A'}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="User-contact-info">
                                        <div class="User-contact-info-icon">
                                            <img src="{{ asset('images/call.svg') }}">
                                        </div>
                                        <div class="User-contact-info-content">
                                            <h2>Phone Number</h2>
                                            @if(!empty($users->phone))
                                                <p>+1{{ $users->phone }}</p>
                                            @else
                                                <p>N/A</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="User-contact-info">
                                        <div class="User-contact-info-icon">
                                            <img src="{{ asset('images/location.svg') }}">
                                        </div>
                                        <div class="User-contact-info-content">
                                            <h2>Address</h2>
                                            <p>{{$users->full_address ?? 'N/A'}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-info-item-action" style="cursor: pointer;">
                                <!-- <a class="EditProfilebtn" data-bs-toggle="modal" data-bs-target="#EditProfile">Edit Profile</a> -->
                                <a class="ChangePasswordbtn" data-bs-toggle="modal" data-bs-target="#ChangePassword">Change Password</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="modal lm-modal fade" id="ChangePassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ss-modal-form">
                    <h2>Change Password</h2>
                    <form id="changePasswordForm">
                        @csrf
                        <div class="row">
                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <input type="password" name="old_password" id="old_password"  class="form-control" placeholder="Enter Old Password" required>
                                    <span toggle="#old_password" class="eye-toggle fa fa-fw fa-eye field-icon toggle-password"></span>
                                </div>
                            </div> -->

                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Create New Password" required>
                                    <span toggle="#password" class="eye-toggle fa fa-fw fa-eye field-icon toggle-password"></span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="password" name="password_confirmation"  id="password_confirmation" class="form-control" placeholder="Confirm New Password" required>
                                    <span toggle="#password_confirmation" class="eye-toggle fa fa-fw fa-eye field-icon toggle-password"></span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                    <button type="submit" class="save-btn">Change Password</button>
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
    <!-- Edit Profile -->
    <div class="modal ss-modal fade" id="EditProfile" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
                <h2 class="modal-title" style="color: var(--pink);margin-left: 171px;margin-top: 14px;font-size: 20px;">Edit Profile</h2>
                <button type="button" class="close" id="closeModalBtn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="ss-modal-form">
                    <form method="POST" action="{{ route('manufacturers.update', $users->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 0rem !important;">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" id="full_name" name="full_name" required class="form-control" placeholder="Full Name" value="{{$user->full_name}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0rem !important;">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" required class="form-control" placeholder="Email Address" value="{{$user->email}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0rem !important;">
                                    <label for="phone">Phone Number</label>
                                    <div class="form-group-phone">
                                                <span class="input-group-text">+1</span>
                                                <div class="input-group-form-control">
                                    <input type="text" id="phone" name="phone" required class="form-control phone" placeholder="Phone Number" value="{{$user->mobile}}" maxlength="10"> 
                                    <div class="invalid-feedback">Please enter a 10-digit phone number.</div>
                                    </div>
                                    
                                </div>
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                                             
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 0rem !important;">
                                    <label for="manufacturer_name">Business Name</label>
                                    <input type="text" id="manufacturer_name" name="manufacturer_name" required class="form-control" placeholder="Business Name" value="{{$user->manufacturer_name}}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 0rem !important;">
                                    <label for="manufacturer_logo">Business Logo</label>
                                    <input type="file" id="manufacturer_logo" name="manufacturer_image" class="form-control" placeholder="Business Logo" onchange="previewImage(event)">
                                </div>
                                @if (!empty($attributes->attribute_value))
                                <div class="upload-file-item" >
                                    <div class="upload-file-item-content">
                                        <div class="upload-file-media">
                                            <img id="preview_image" src="{{ asset('upload/manufacturer-image/'.$attributes->attribute_value) }}">
                                        </div>
                                    </div>
                                    <div class="upload-file-action">
                                        <a class="delete-btn" href="#" data-image-id="{{ $attributes->id }}"><img src="{{ asset('images/close-circle.svg') }}"></a>
                                    </div>
                                </div>
                                @else 
                                <div class="upload-file-item" style="display: none;">
                                    <div class="upload-file-item-content">
                                        <div class="upload-file-media">
                                            <img id="preview_image" src="">
                                        </div>
                                    </div>
                                    <!-- <div class="upload-file-action">
                                        <a class="delete-btn" href="#"><img src="{{ asset('images/close-circle.svg') }}"></a>
                                    </div> -->
                                </div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <div class="form-group form-group-icon" >
                                    <label for="location">Location</label>
                                    <input id="geocoder" type="search" id="location" name="location" required class="form-control" placeholder="Search for location" value="{{$user->manufacturer_address}}">
                                    <span class="form-input-icon"><img src="{{ asset('images/location.svg') }}" style="margin-top: 25px;"></span>
                                </div>
                            </div>

                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password"  name="new_password" class="form-control" placeholder="Create New Password">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password"  name="confirm_password" class="form-control" placeholder="Confirm New Password">
                                </div>
                            </div> -->

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#closeModalBtn').on('click', function() {
                    $('#EditProfile').modal('hide');
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

                $('form').on('submit', function(e) {
        var phoneNumber = $('#phone').val().replace(/\D/g, ''); // Get the phone number without non-numeric characters
        if (phoneNumber.length !== 10) {
            e.preventDefault(); // Prevent form submission
            $('#phone').addClass('is-invalid'); // Add 'is-invalid' class to indicate error
        }
    });
            });
        </script>
        <script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        
        var form = e.target;
        var formData = new FormData(form);

        fetch('{{ route("manufacturer.updatePassword", ["id" => Auth::user()->id]) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDtg_iY8FedOwjt419T7zaT0fHTcTYcwPE&libraries=places"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('geocoder');
        var autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener('place_changed', function () {
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
        document.getElementById('geocoder').addEventListener('change', function () {
            if (document.getElementById('geocoder').value === '') {
                document.getElementById('full_address').value = '';
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
                document.getElementById('locationSelected').value = 'false';
            }
        });

        // Form submission validation
        // document.getElementById('add-plant-form').addEventListener('submit', function(e) {
        //     var locationSelected = document.getElementById('locationSelected').value;
            
        //     // Validation: Check if location is selected
        //     if (locationSelected == 'true') {
        //         alert('Please select a location from the dropdown.');
        //         e.preventDefault(); // Prevent form submission
        //         return; // Exit the function to avoid showing the loader
        //     }
            
        //     // Show the loader if the form is valid
        //     document.getElementById('loader').style.display = 'block';
        // });
    });
</script>
<script>
      function previewImage(event) {
        var input = event.target;
        var preview = document.getElementById('preview_image');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                // Show the upload-file-item div if an image is selected
                document.querySelector('.upload-file-item').style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = "";
            // Hide the upload-file-item div if no image is selected
            document.querySelector('.upload-file-item').style.display = 'none';
        }
    }
</script>
<script>
    // JavaScript to toggle password visibility
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


    document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();
            
            const imageId = this.getAttribute('data-image-id');
            const imageElement = this.closest('.upload-file-item');
            
            try {
                const response = await fetch("{{ route('delete-profile-photo', ':id') }}".replace(':id', imageId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: imageId })
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
