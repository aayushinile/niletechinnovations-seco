@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/communityowners.css') }}">
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
    </style>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-card">
            <div class="card-header">
                <h2>Plant Detail</h2>
                <div class="search-filter wd2 d-none">
                    <div class="row g-1">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="btn-bl"data-bs-toggle="modal" data-bs-target="#MarkAsInactive">Mark As
                                    {{ $mfs->status == 1 ? 'Inactive' : 'Active' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="plants-details-head" style="justify-content:end;">
                <div class="plants-details-action">
                    <a class="ChangePasswordbtn" data-bs-toggle="modal" data-bs-target="#ChangePassword" style="background: var(--green);color: var(--white);padding: 12px 20px;border-radius: 5px;font-size: 14px;box-shadow: 0 4px 10px #5f0f5845;display: inline-block;position: relative;cursor:pointer" data-plant-id="{{ $mfs['id'] }}" data-email-id="{{ $mfs['email'] }}">Reset Credentials</a>
                </div>
            </div>
            </div>
            <div class="card-body">
                <div class="user-table-item">
                    <div class="row g-1 align-items-center">
                        <div class="col-md-4">
                            <div class="user-profile-item">
                                <div class="user-profile-media"><img src="{{ asset('images/defaultuser.png') }}">
                                </div>
                                <div class="user-profile-text">
                                    <h2>{{ $mfs->business_name ?? 'N/A' }}</h2>
                                    <div
                                        class="status-text  {{ $mfs->status == 1 ? 'status-active' : 'status-inactive' }}">
                                        {{ $mfs->status == 1 ? 'Active' : 'Inactive' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-1 align-items-center">


                                <div class="col-md-6">
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
                                <div class="col-md-6">
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


                


                <div class="added-comminity-section">
                <div class="row d-flex">
    <div class="col-md-12">
        <div class="added-comminity-card">
            <div class="added-comminity-head">
                <div class="col-md-10">
                    <h2>Added Plants ({{ count($plants) }})</h2>
                </div>
                @if ($plants->isNotEmpty())
                <div class="col-md-2 text-right">
                    <form action="{{ route('admin.plant.export') }}" method="GET">
                        <input type="hidden" value="{{$mfs->id}}" name="id">
                        <button type="submit" class="btnDownloadExcel">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            <div class="added-comminity-body">
                @foreach ($plants as $item)
                    <div class="added-comminity-item">
                        <div class="added-comminity-item-image">
                            <img src="{{ asset('images/defaultuser.png') }}">
                        </div>
                        <div class="added-comminity-item-text">
                            <h4>{{ $item->plant_name }}</h4>
                            <div class="added-comminity-location">
                                <img src="{{ asset('admin/images/location.svg') }}">
                                {{ $item->full_address }}
                            </div>
                            <a class="Viewbtn" href="{{ route('admin.manufracturers.show', encrypt($item->plant_id)) }}">View</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
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
                    <h2>Reset Credentials</h2>
                    <form id="changePasswordForm">
                        @csrf
                        <input type="hidden" id="plant-id" name="plant_id">
                        <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email">Email</label> <!-- Label for the email field -->
                                <input type="email" name="email" id="email" class="form-control" required> <!-- Read-only email input field -->
                            </div>
                        </div>
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
                                    <button type="submit" class="save-btn mb-2">Reset</button>
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
<div class="loader-container" id="loader">
                <div class="loader"></div>
            </div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).on('click', '.ChangePasswordbtn', function() {
        var plantId = $(this).data('plant-id');
        var emailId = $(this).data('email-id');

        // Set the plant ID and email in the modal
        $('#plant-id').val(plantId);
        $('#email').val(emailId); // Set the email in the new input field
    });
</script>
<script>
    
document.addEventListener("DOMContentLoaded", function() {
    let plantId = null;
    document.querySelectorAll('.ChangePasswordbtn').forEach(function(button) {
        button.addEventListener('click', function() {
            plantId = this.getAttribute('data-plant-id');
            document.getElementById('plant-id').value = plantId; // Set the hidden input field value
        });
    });
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        document.getElementById('loader').style.display = 'block';
        var form = e.target;
        var formData = new FormData(form);
        var manufacturerId = document.getElementById('plant-id').value;
        console.log(manufacturerId);
        var email = document.getElementById('email').value;
        formData.append('email', email);
        const url = `{{ route("admin.updatePassword", ["id" => ":id"]) }}`.replace(':id', plantId);


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
            document.getElementById('loader').style.display = 'none';
            var messageContainer = document.getElementById('changePasswordMessage');
            let modal = new bootstrap.Modal(document.getElementById('ChangePassword'));

            if (data.errors) {
                document.getElementById('loader').style.display = 'none';
                messageContainer.innerHTML = '<div class="alert alert-danger">' + data.errors.join('<br>') + '</div>';
            } else {
                document.getElementById('loader').style.display = 'none';
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

