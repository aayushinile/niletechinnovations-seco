
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Show Search</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') }}">
	<script src="{{asset('js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/managelocations.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .form-group {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="auth-section auth-height">
        <div class="auth-bg-video">
            <img src="{{asset('images/backgoundhome.jpg')}}" id="background-media">
        </div>
        <div class="auth-content-card wd40">
            <div class="container">
                <div class="auth-card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="auth-form">
                                <div class="brand-logo">
                                    <img src="{{asset('images/logo.svg')}}" alt="logo" >
                                </div>
                                <h2>Manufacturer Signup</h2>
                                <p>Please Enter You Basic Details</p>
                                <form class="pt-4" method="post" action="{{ route('saveManufacturer') }}" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <div class="row">

                                    <div class="col-md-12 d-none">
                                            <div class="form-group">
                                                <input type="text" name="manufacturer_full_name" class="form-control @error('manufacturer_full_name') is-invalid @enderror" placeholder="Manufacturer Name " value="{{ old('manufacturer_full_name') }}">
                                                @error('manufacturer_full_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" placeholder="Manufacturer Name " value="{{ old('full_name') }}">
                                                @error('full_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input id="email" type="email" name="email" required class="form-control @error('email') is-invalid @enderror" placeholder="Plant Email Address *" value="{{ old('email') }}" autocomplete="new-email" onkeyup="checkEmail()">
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <div id="email-feedback" class="invalid-feedback" style="display: none;"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                        <div class="form-group-phone">
                                                    <span class="input-group-text">+1</span>
                                                    <div class="input-group-form-control">
                                                <input type="text" name="mobile"  class="form-control @error('mobile') is-invalid @enderror phone" placeholder="Plant Phone Number" value="{{ old('mobile') }}" maxlength="10">
                                                @error('mobile')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <div class="invalid-feedback">Please enter a 10-digit phone number.</div>
                                            </div>
                                        </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" name="manufacturer_name" class="form-control @error('manufacturer_name') is-invalid @enderror" placeholder="Business Name" value="{{ old('manufacturer_name') }}">
                                                @error('manufacturer_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="file" name="manufacturer_image" accept=".jpg,.jpeg,.png" class="form-control @error('manufacturer_image') is-invalid @enderror"  onchange="previewImage(event)"> 
                                                @error('manufacturer_image')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="upload-file-item" style="display: none;">
                                                <div class="upload-file-item-content">
                                                    <div class="upload-file-media">
                                                        <img id="preview_image" src="">
                                                    </div>
                                                </div>
                                                <div class="upload-file-action">
                                                    <a class="delete-btn" href="#" onclick="deleteImage(event)"><img src="{{ asset('images/close-circle.svg') }}"></a>
                                                </div>
                                            </div>
                                         </div>

                                        <div class="col-md-12">
                                            <div class="form-group form-group-icon">
                                            <input id="geocoder" class="form-control" type="text" placeholder="Manufacturer Address *" required name="manufacturer_address" required class="form-control @error('manufacturer_address') is-invalid @enderror" placeholder="Search for location" value="{{ old('manufacturer_address') }}">
                                            <input type="hidden" id="full_address" name="full_address" required class="form-control">
                                            <input type="hidden" id="latitude" name="latitude">
                                            <input type="hidden" id="longitude" name="longitude">
                                            <input type="hidden" id="locationSelected" name="locationSelected" value="false">
                                                <span class="form-input-icon"><img src="{{asset('images/location.svg')}}"></span>
                                                @error('manufacturer_address')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="password" name="password" required class="form-control @error('password') is-invalid @enderror" placeholder="Create New Password *" id="password">
                                                <span class="eye-icon" onclick="togglePasswordVisibility('password', this)">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="password" name="password_confirmation" id="password_confirmation" required class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm New Password *">
                                                <span class="eye-icon" onclick="togglePasswordVisibility('password_confirmation', this)">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                                @error('password_confirmation')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <button type="submit" class="auth-form-btn">Signup</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-1 forgotpsw-text">
                                        <a href="{{ url('manufacturer/login') }}">Already Have An Account? <b>Login</b></a>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    function deleteImage(event) {
        event.preventDefault();
        var preview = document.getElementById('preview_image');
        var input = document.querySelector('input[name="manufacturer_image"]');

        // Clear the input value
        input.value = "";
        // Clear the preview image
        preview.src = "";
        // Hide the upload-file-item div
        document.querySelector('.upload-file-item').style.display = 'none';
    }



    function togglePasswordVisibility(fieldId, icon) {
        var field = document.getElementById(fieldId);
        if (field.type === "password") {
            field.type = "text";
            icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            field.type = "password";
            icon.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }

    let emailIsValid = true;
    function checkEmail() {
        const emailInput = document.getElementById('email');
        const feedbackDiv = document.getElementById('email-feedback');
        const submitButton = document.querySelector('button[type="submit"]');
        
        // Clear previous feedback
        feedbackDiv.style.display = 'none';
        feedbackDiv.textContent = '';

        const email = emailInput.value;
        
        if (email.length > 0) {
            // Fetch CSRF token from the meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ route('checkEmail') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    feedbackDiv.textContent = 'This email is already registered.';
                    feedbackDiv.style.display = 'block';
                    emailIsValid = false;
                    submitButton.disabled = true; // Disable the submit button
                } else {
                    emailIsValid = true;
                    submitButton.disabled = false; // Enable the submit button
                }
            })
            .catch(error => console.error('Error:', error));
        } else {
            emailIsValid = true;
            submitButton.disabled = false; // Enable the submit button if email is empty
        }
    }

    // Check email on form submit
    document.querySelector('form').addEventListener('submit', function(event) {
        if (!emailIsValid) {
            event.preventDefault(); // Prevent form submission if email is invalid
            alert('Please correct the email address before submitting.');
        }
    });
</script>
</body>
</html>

