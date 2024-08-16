<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Show Search</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/auth.css') }}">
    <script src="{{ asset('admin/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
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

        .swal2-confirm {
            background-color: #5F0F58;
        }

        .password-container {
    position: relative;
}

.password-container .form-control {
    padding-right: 40px; /* Adjust the padding to fit the eye icon */
}

.eye-icon {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
    color: #aaa; /* Adjust color as needed */
}
    </style>
</head>

<body>
    <div class="auth-section auth-height align-items-center">
        <div class="auth-bg-video">
            <img src="images/backgoundhome.jpg" id="background-media">
        </div>
        <div class="auth-content-card wd35">
            <div class="container">
                <div class="auth-card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="auth-form">
                                <div class="brand-logo">
                                    <img src="images/logo.svg" alt="logo">
                                </div>
                                <h2>Administrator Login</h2>
                                <p>To Get Into Show Search Control Panal</p>
                                <form class="pt-4" method="post" id="signin_form"
                                    action="{{ route('admin.authenticate') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" name="email" required class="form-control"
                                            placeholder="Email Address">
                                    </div>
                                    <div class="form-group">
                                        <div class="password-container">
                                            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                                            <span class="eye-icon" onclick="togglePassword()">
                                                <i class="fas fa-eye-slash" id="eye"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="auth-form-btn">Login</button>
                                    </div>

                                    <div class="mt-1 forgotpsw-text">
                                        <a href="{{ route('admin.forget.password') }}">I forgot my password</a>
                                    </div>
                                </form>
                                @if ($errors->any())
                                    <script>
                                        var errors = {!! json_encode($errors->all()) !!};
                                        var errorMessage = errors.join('\n');
                                        alert(errorMessage);
                                    </script>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        $(document).ready(function() {
            $('#signin_form').validate({
                rules: {
                    email: {
                        required: true,
                        maxlength: 191,
                        email: true
                    },
                    password: {
                        required: true,
                        maxlength: 191,

                    },
                },
                errorElement: "span",
                errorPlacement: function(error, element) {
                    error.addClass("text-danger ml-4");
                    element.closest(".form-group").append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $('.please-wait').click();
                    $(element).addClass("text-danger ml-4");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass("text-danger ml-4");
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    let formData = new FormData(form);

                    $.ajax({
                        type: 'post',
                        url: form.action,
                        data: formData,
                        dataType: 'json',
                        contentType: false,
                        processData: false,

                        success: function(response) {
                            if (response.status == 200) {

                                Swal.fire({
                                    title: 'Success',
                                    text: response.message,
                                    icon: 'success',

                                }).then((result) => {

                                    if (response.redirect == true) {
                                        window.location = response.route;
                                    }
                                    // var url = $('#redirect_url').val();
                                    // if (url !== undefined || url != null) {
                                    //     window.location = url;
                                    // } else {
                                    //     location.reload(true);
                                    // }
                                })

                                return false;
                            }

                            if (response.status == 201) {
                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );

                                return false;
                            }
                        },
                        error: function(data) {
                            if (data.status == 422) {
                                var form = $("#signin_form");
                                let li_htm = '';
                                $.each(data.responseJSON.errors, function(k, v) {
                                    const $input = form.find(
                                        `input[name=${k}],select[name=${k}],textarea[name=${k}]`
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

                                return false;
                            } else {
                                Swal.fire(
                                    'Error',
                                    data.statusText,
                                    'error'
                                );
                            }
                            return false;

                        }
                    });
                }
            })
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
         function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    }
    </script>
</body>

</html>
