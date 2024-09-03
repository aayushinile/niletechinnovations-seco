@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/profile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .Ownertext {
            font-size: 21px;
        }

        .form-group {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: black;
        }
    </style>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-heading-section">
            <h2>My Profile</h2>
        </div>
        <div class="listed-plants-section">
            <div class="profile-info-item">
                <div class="profile-info-item-media">
                @auth('admin')
                        @php
                            $admin = auth('admin')->user(); // Fetch currently authenticated admin
                        @endphp
                    <a href="#"><img
                            src="{{ $admin->profile_image ? asset($admin->profile_image) : asset('admin/images/profile.png') }}"></a>
                         @if ($admin->profile_image != null)
                        <div class="profile-media-action">
                            <a  data-bs-toggle="modal"
                            data-bs-target="#activePlant" style="cursor: pointer;"><i class="fa-regular fa-trash-can"></i></a>
                        </div>
                        @endif
                </div>
                <div class="profile-info-item-content">
                    
                        @if ($admin)
                            <h2>{{ $admin->name }}</h2>
                            <div class="profile-info-point">
                                <div class="Ownertext">Admin</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="User-contact-info">
                                        <div class="User-contact-info-icon">
                                            <img src="images/sms.svg">
                                        </div>
                                        <div class="User-contact-info-content">
                                            <h2>Email Address</h2>
                                            <p>{{ $admin->email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="User-contact-info">
                                        <div class="User-contact-info-icon">
                                            <img src="images/call.svg">
                                        </div>
                                        <div class="User-contact-info-content">
                                            <h2>Phone Number</h2>
                                            <p>+1 {{ $admin->mobile }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p>No admin data found.</p>
                        @endif
                    @else
                        <p>You are not logged in as an admin.</p>
                    @endauth
                    <div class="profile-info-item-action">

                        <a class="EditProfilebtn" data-bs-toggle="modal" data-bs-target="#EditProfile" style="cursor: pointer;"> Edit Profile</a>
                        <a class="ChangePasswordbtn" data-bs-toggle="modal" data-bs-target="#ChangePassword" style="cursor: pointer;"> Change
                            Password</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Change Password -->
    <div class="modal lm-modal fade" id="ChangePassword" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-form">
                        <h2>Change Password</h2>
                        <form action="{{ route('admin.change.password') }}" method="POST">
                            @csrf

                            <div class="row">
                                <!-- <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Enter Old Password" id="currentPassword" required>

                                        <i class="fas fa-eye toggle-password" data-toggle="#currentPassword"></i>
                                    </div>
                                </div> -->

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="password" name="new_password" class="form-control" id="new_password"
                                            placeholder="Create New Password" required>
                                        <i class="fas fa-eye toggle-password" data-toggle="#new_password"></i>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="password" name="new_password_confirmation" class="form-control"
                                            id="new_password_confirmation" placeholder="Confirm New Password" required>
                                        <i class="fas fa-eye toggle-password" data-toggle="#new_password_confirmation"></i>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">

                                        <button type="submit" class="save-btn mb-2">Change Password</button>
                                        <button type="button" class="cancel-btn" data-bs-dismiss="modal"
                                            aria-label="Close">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </form>
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
                <a  class="close" id="closeModalBtn" data-dismiss="modal" aria-label="Close" style="font-size:25px;margin-left:11rem;cursor:pointer">
                    <span aria-hidden="true">&times;</span>
                </a>
            </div>
                <div class="modal-body">
                    <div class="ss-modal-form">
                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text" name="fullname" required="" class="form-control"
                                            placeholder="Full Name" value="{{ auth('admin')->user()->fullname }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="email" name="email" required="" class="form-control"
                                            placeholder="Email Address" value="{{ auth('admin')->user()->email }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <div class="d-flex" style="gap:5px;align-items:baseline">
                                    <span class="input-group-text">+1</span>
                                        <input type="phone" name="mobile" required="" class="form-control"
                                            placeholder="phone" data-inputmask="'mask': '(999) 999-9999'"
                                            value="{{ auth('admin')->user()->mobile }}">
                                    </div>
                                </div>
                                </div>



                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="file" name="file" class="form-control"  onchange="previewImage(event)">
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
                                    <div class="form-group">
                                        <button type='submit' class="save-btn">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
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
                        {{-- <div class="ss-modal-delete-icon"><img src=""></div> --}}
                        <p id="delete-message">Are you sure you want to remove Profile Picture?</p>
                        <form method="POST" action="{{ route('admin.remove.profile') }}">
                            @csrf


                            <div class="ss-modal-delete-action">
                                <button type="submit" id="confirm-activate" class="yes-btn">Yes, Confirm</button>
                                <button type="button" class="cancel-btn" data-bs-dismiss="modal"
                                    aria-label="Close">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script>
            $(document).ready(function() {
                $('#closeModalBtn').on('click', function() {
                    $('#EditProfile').modal('hide');
                });
                

                
            });
        </script>
    <script>
        document.querySelectorAll('.toggle-password').forEach(item => {
            item.addEventListener('click', event => {
                const input = document.querySelector(item.getAttribute('data-toggle'));
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                item.classList.toggle('fa-eye-slash');
            });
        });


        $(":input").inputmask();
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

        function deleteImage(event) {
            event.preventDefault();
            var preview = document.getElementById('preview_image');
            var input = document.querySelector('input[name="file"]');

            // Clear the input value
            input.value = "";
            // Clear the preview image
            preview.src = "";
            // Hide the upload-file-item div
            document.querySelector('.upload-file-item').style.display = 'none';
        }
    </script>
@endsection
