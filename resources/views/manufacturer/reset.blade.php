<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Show Search</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/auth.css') }}">
	<script src="{{asset('js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/managelocations.css')}}">
    <link rel="stylesheet" type="text/css" href="css/auth.css">
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
    </style>
</head>

<body>
    <div class="auth-section auth-height align-items-center">
        <div class="auth-bg-video">
            <img src="{{asset('images/backgoundhome.jpg')}}" id="background-media">
        </div>
        <div class="auth-content-card" style="width: 35% !important;">
            <div class="container">
                <div class="auth-card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="auth-form">
                                <div class="brand-logo">
                                    <img src="{{asset('images/logo.svg')}}" alt="logo">
                                </div>
                                <h2>Reset Password?</h2>
                                <form class="pt-4" method="post" action="{{ route('manufacturer.reset.password.post') }}"
                                    id="signin_form">
                                    @csrf
                                    <input type="hidden" id="redirect_url" value="{{ route('manufacturer.login') }}">

                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control"
                                                id="passwordField" placeholder="New Password">
                                            <div class="input-group-append d-flex">
                                                <span class="px-2 m-auto" type="button" id="togglePassword">
                                                    <i class="fa fa-eye-slash text-dark" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input id="password-confirm" placeholder="Confirm New Password" type="password"
                                            class="form-control" name="password_confirmation" required
                                            autocomplete="new-password">

                                    </div>


                                    <div class="form-group">
                                        <button type="submit" class="auth-form-btn">Reset Password</button>
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

                                $(".loading").addClass("d-none");

                                Swal.fire(
                                    'Success',
                                    response.message,
                                    'success'
                                );


                                $("#otp").show();
                                $("#email").attr("readonly", "true");
                                $("#otp_input").attr("required", "true");
                                $(".auth-form-btn").text("Submit");

                                setTimeout(function() {
                                    var url = $('#redirect_url').val();
                                    if (url !== undefined || url != null) {
                                        window.location = url;
                                    } else {
                                        window.location = '/'
                                    }
                                }, 2000);
                                return false;
                            }

                            if (response.success == false) {

                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );
                                $(".loading").addClass("d-none");

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
                                $(".loading").addClass("d-none");

                                return false;
                            } else {

                                toastr.error(data.statusText);
                                $(".loading").addClass("d-none");

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
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            var passwordField = document.getElementById('passwordField');
            var fieldType = passwordField.getAttribute('type');
            if (fieldType === 'password') {
                passwordField.setAttribute('type', 'text');

                this.innerHTML = '<i class="fa fa-eye text-dark" aria-hidden="true"></i>';
            } else {
                passwordField.setAttribute('type', 'password');
                this.innerHTML = '<i class="fa fa-eye-slash text-dark" aria-hidden="true"></i>';
            }
        });
    </script>
</body>

</html>
