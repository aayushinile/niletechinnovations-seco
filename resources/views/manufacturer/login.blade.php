
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Show Search</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/header-footer.css') }}">
    
	<script src="{{asset('js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .field-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }

        .field-icon:hover {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="auth-section auth-height align-items-center">
        <div class="auth-bg-video">
            <img src="{{asset('images/backgoundhome.jpg')}}" id="background-media">
        </div>
        <div class="auth-content-card wd35">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
            <div class="container">
                <div class="auth-card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="auth-form">
                                <div class="brand-logo">
                                    <img src="{{asset('images/logo.svg')}}" alt="logo">
                                </div>
                                <h2>Manufacturer/Plant/Corporate Login</h2>
                                <p>To Get Into Show Search Control Panel</p>
                                <form class="pt-4" method="post" action="{{ url('manufacturer/login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" name="email" required class="form-control @error('email') is-invalid @enderror" placeholder="Email Address" value="{{ old('email') }}">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group position-relative">
                                        <input type="password" name="password" id="password" required class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                        <span toggle="#password" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="auth-form-btn">Login</button>
                                        <a href="{{ url('signup') }}" class="btn-auth-gr" data-bs-toggle="modal" data-bs-target="#signupPlant">Signup As A New Manufacturer/Plant</a>
                                    </div>
                                    <div class="mt-1 forgotpsw-text" >
                                        <a href="{{ route('manufacturer.forget.password') }}" style="text-decoration: none !important;"><b style="text-decoration: underline !important;">Forgot Password </b>?</a>
                                    </div>
                                    
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <div class="modal ss-modal fade" id="signupPlant" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ss-modal-delete">
                    <p id="delete-message" style="font-weight:500">You Are</p>
                    <form id="signup-form" method="POST">
                        @csrf
                        <div class="ss-modal-delete-action">
                            <div class="col-md-12 mb-4">
                                <div class="form-group">
                                    <div class="ss-rep-list">
                                        <div class="ssradiobox">
                                            <input type="radio" name="rep_type" id="plant_rep" value="plant_rep" required checked>
                                            <label for="plant_rep">Plant Representative</label>
                                        </div>

                                        <div class="ssradiobox">
                                            <input type="radio" name="rep_type" id="corp_rep" value="corp_rep" required>
                                            <label for="corp_rep">Corporate Representative</label>
                                        </div>
                                        @error('rep_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ss-modal-delete-action">
                            <button type="button" id="confirm-signup" class="yes-btn">Continue</button>
                            <button type="button" class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('.toggle-password');
            const passwordField = document.getElementById('password');

            togglePassword.addEventListener('click', function () {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
                this.classList.toggle('fa-eye');
            });
        });
    </script>
    <script>
         $(document).ready(function() {
        $('#confirm-signup').on('click', function() {
            var selectedRole = $('input[name="rep_type"]:checked').val();

            if (selectedRole === 'plant_rep') {
                // Redirect to plant representative signup route
                window.location.href = "{{ url('signup') }}";
            } else if (selectedRole === 'corp_rep') {
                // Redirect to corporate representative signup route
                window.location.href = "{{ url('signup/corporate') }}";
            } else {
                alert('Please select a role.');
            }
        });
    });
    </script>
</body>
</html>

