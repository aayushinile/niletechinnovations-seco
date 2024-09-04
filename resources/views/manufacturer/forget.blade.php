<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Show Search</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') }}">
	<script src="{{asset('js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/managelocations.css')}}">
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('plugins/jquery-validation/jquery.validate.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        .input-group {
            border: 1px solid gray;
            /* Add border to input group */
            border-radius: .25rem;
            /* Optional: Add border radius for input group */
            width: 100%;
            display: flex
                /* Ensure input group stretches to full width */
        }

        .input-group .form-control {
            border: none;
            /* Remove border from input field */
            border-radius: 0;
            /* Optional: Remove border radius from input field */
            box-shadow: none;
            flex: 1;
            /* Optional: Remove any box shadow */
        }

        #passwordField {
            border: none;
            outline: none;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .swal2-confirm {
            background-color: #5F0F58;
        }

        .loader-container {
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
            display: none; /* Initially hidden */
            justify-content: center;
            align-items: center;
        }

        .loader {
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #3498db; /* Blue */
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="auth-section auth-height align-items-center">
        <div class="auth-bg-video">
            <img src="{{asset('images/backgoundhome.jpg')}}" id="background-media">
        </div>
        <div class="auth-content-card wd35">
            <div class="container">
                <div class="auth-card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="auth-form">
                                <div class="brand-logo">
                                    <img src="{{asset('images/logo.svg')}}" alt="logo">
                                </div>
                                <h2>Forgot Password?</h2>
                                <form class="pt-4" method="post" action="{{ route('manufacturer.forget.password.post') }}"
                                    id="signin_form">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" name="email" required class="form-control"
                                            placeholder="Email Address">
                                    </div>

                                    <div class="form-group  otp" id="otp" style="display: none">
                                        <input type="number" min="100000" max="999999" name="otp"
                                            id="otp_input" class="form-control" placeholder="Enter OTP">
                                        @error('otp')
                                            <span class="text-danger p-2" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <button type="submit" class="auth-form-btn">Reset</button>
                                    </div>

                                    <div class="mt-1 forgotpsw-text">
                                        <a  class="btn-auth-gr" href="{{ route('manufacturer.login') }}">Signin</a>
                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="loader-container" id="loader">
            <div class="loader"></div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#signin_form').validate({
                rules: {


                    email: {
                        required: true,
                        email: true
                    },

                    otp: {
                        required: true,
                        maxlength: 6,
                        minlength: 6,

                    },



                },
                errorElement: "span",
                errorPlacement: function(error, element) {
                    // error.addClass("invalid-feedback");
                    // element.closest(".field").append(error);
                    element.addClass("border border-danger")
                },
                highlight: function(element, errorClass, validClass) {
                    $('.please-wait').hide();

                },
                unhighlight: function(element, errorClass, validClass) {
                    // $(element).removeClass("text-danger");
                    $(element).removeClass("border border-danger");

                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    document.getElementById('loader').style.display = 'block';

                    let formData = new FormData(form);



                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true,




                        "disableTimeOut": true,
                    }


                    $(".loading").removeClass("d-none");

                    $.ajax({
                        type: 'post',
                        url: form.getAttribute('action'),
                        data: formData,
                        dataType: 'json',
                        contentType: false,
                        processData: false,

                        success: function(response) {
                            if (response.success == true) {
                                if (response.redirect) {
                                    window.location = response.message;
                                    return false;
                                }

                                document.getElementById('loader').style.display = 'none';

                                Swal.fire(
                                    'Success',
                                    response.message,
                                    'success'
                                );


                                $("#otp").show();
                                $("#email").attr("readonly", "true");
                                $("#otp_input").attr("required", "true");
                                $(".auth-form-btn").text("Submit");

                                // setTimeout(function() {
                                //     var url = $('#redirect_url').val();
                                //     if (url !== undefined || url != null) {
                                //         window.location = url;
                                //     } else {
                                //         window.location = '/'
                                //     }
                                // }, 2000);
                                return false;
                            }

                            if (response.success == false) {

                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );
                                document.getElementById('loader').style.display = 'none';

                                return false;
                            }
                        },
                        error: function(data) {
                            if (data.status == 422) {
                                let li_htm = '';
                                $.each(data.responseJSON.errors, function(k, v) {
                                    const $input = form.find(
                                        `input[name=${k}],s$(thisct)[name=${k}],textarea[name=${k}]`
                                    );
                                    if ($input.next('small').length) {
                                        $input.next('small').html(v);
                                        if (k == 'services' || k == 'membership') {
                                            $('#myselect').next('small').html(v);
                                        }
                                    } else {
                                        $input.after(
                                            `<small class='text-danger'>${v}</small>`
                                        );
                                        if (k == 'services' || k == 'membership') {
                                            $('#myselect').after(
                                                `<small class='text-danger'>${v[0]}</small>`
                                            );
                                        }
                                    }
                                    li_htm += `<li>${v}</li>`;
                                });
                                document.getElementById('loader').style.display = 'none';

                                return false;
                            } else {

                                toastr.error(data.statusText);
                                document.getElementById('loader').style.display = 'none';

                                return false;
                            }
                        }
                    });
                }
            });

        });
    </script>
    @if (Session::has('success'))
        <script>
            Swal.fire('Success', '{{ Session('success') }}', 'success');
        </script>
    @endif

    @if (Session::has('error'))
        <script>
            Swal.fire('Error', '{{ Session('error') }}', 'error');
        </script>
    @endif
</body>

</html>
