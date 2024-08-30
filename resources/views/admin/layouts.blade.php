<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Show Search</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/header-footer.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/plugins/OwlCarousel/assets/owl.carousel.min.css') }}">
    <script src="{{ asset('admin/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/plugins/OwlCarousel/owl.carousel.min.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/plugins/fancybox/fancybox.css') }}">

    <script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/plugins/fancybox/fancybox.umd.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/function.js') }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/managelocations.css') }}">
    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/line-awesome@1.3.0/dist/line-awesome/css/line-awesome.min.css">
    <script src="{{ asset('plugins/select2/select2.min.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/select2/select2.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @stack('css')
    <style>
        button.btn-MarkAsInactive {
            outline: none;
            width: 100%;
            padding: 15px 40px;
            display: inline-block;
            color: var(--white);
            font-size: 14px;
            font-weight: 600;
            border-radius: 5px;
            border: none;
            box-shadow: 0px 8px 13px 0px rgba(0, 0, 0, 0.05);
            background: var(--pink);
            margin-bottom: 5px;
        }

        .swal2-confirm {
            background-color: #5F0F58;
        }

        .calendar-input-group {
            position: relative;
        }

        .calendar-icon-info {
            position: absolute;
            right: 10px;
            top: 4px;
        }
        .nav-item.active .nav-link-1 {
            background: var(--pink);
            border-radius: 0;
            color: white !important;
        }
        
    </style>

</head>

<body class="main-site">
    <div class="page-body-wrapper">
        <div class="sidebar-wrapper">
            <div class="sidebar-logo">
                <a href="{{ route('admin.dashboard') }}">
                    <img class="" src="{{ asset('admin/images/logo.svg') }}" alt="">
                </a>
                <div class="back-btn"></div>
            </div>
            <div class="sidebar-nav">
                <nav class="sidebar sidebar-offcanvas" id="sidebar">
                    <ul class="nav">
                        <li class="nav-item {{ Route::is('admin.dashboard*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M9.02 2.83992L3.63 7.03992C2.73 7.73992 2 9.22992 2 10.3599V17.7699C2 20.0899 3.89 21.9899 6.21 21.9899H17.79C20.11 21.9899 22 20.0899 22 17.7799V10.4999C22 9.28992 21.19 7.73992 20.2 7.04992L14.02 2.71992C12.62 1.73992 10.37 1.78992 9.02 2.83992Z"
                                            stroke="#5f0f58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M16.5 11.5L12.3 15.7L10.7 13.3L7.5 16.5" stroke="#5f0f58"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M14.5 11.5H16.5V13.5" stroke="#5f0f58" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="menu-title">Dashboard</span>
                            </a>

                        </li>

                        <li class="nav-item {{ Route::is('admin.community*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.community.owners') }}">
                                <span class="menu-icon">
                                    <svg width="26" height="24" viewBox="0 0 26 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M23.0014 12C23.0014 6.48 18.3934 2 12.7156 2C7.03793 2 2.42993 6.48 2.42993 12C2.42993 17.52 7.03793 22 12.7156 22"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M8.60145 3H9.63002C7.6243 8.84 7.6243 15.16 9.63002 21H8.60145"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M15.8013 3C16.799 5.92 17.303 8.96 17.303 12" stroke="#5F0F58"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M3.4585 16V15C6.46192 15.97 9.58878 16.46 12.7156 16.46"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M3.4585 8.99998C9.46535 7.04998 15.9659 7.04998 21.9728 8.99998"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M18.9246 18C18.9026 17.9969 18.8744 17.9969 18.8493 18C18.2974 17.9817 17.8584 17.542 17.8584 17.0015C17.8584 16.4489 18.3162 16 18.887 16C19.4546 16 19.9155 16.4489 19.9155 17.0015C19.9124 17.542 19.4765 17.9817 18.9246 18Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M21.9727 20.8027C21.1578 21.5476 20.0773 22 18.887 22C17.6966 22 16.6162 21.5476 15.8013 20.8027C15.8471 20.3732 16.1217 19.9528 16.6116 19.6238C17.866 18.7921 19.9171 18.7921 21.1624 19.6238C21.6522 19.9528 21.9269 20.3732 21.9727 20.8027Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M18.887 22C21.1592 22 23.0013 20.2091 23.0013 18C23.0013 15.7909 21.1592 14 18.887 14C16.6147 14 14.7727 15.7909 14.7727 18C14.7727 20.2091 16.6147 22 18.887 22Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="menu-title">Community/Retailers</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Route::is('admin.manufracturers*') ? 'active' : '' }}">
                            <a class="nav-link" data-bs-toggle="collapse" href="#Manufacturersdropdown" >
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 22H22" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M2.94995 22L2.99995 9.96999C2.99995 9.35999 3.28995 8.78004 3.76995 8.40004L10.77 2.95003C11.49 2.39003 12.5 2.39003 13.23 2.95003L20.23 8.39003C20.72 8.77003 21 9.34999 21 9.96999V22"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linejoin="round" />
                                        <path
                                            d="M15.5 11H8.5C7.67 11 7 11.67 7 12.5V22H17V12.5C17 11.67 16.33 11 15.5 11Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M10 16.25V17.75" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M10.5 7.5H13.5" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="menu-title">Manufacturers</span>
                                <span class="dropdown-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
                            </a>
                            <ul class="collapse {{ Route::is('admin.manufracturers*') || Route::is('admin.manufracturers.corporate') ? 'show' : '' }}" id="Manufacturersdropdown">
                                <li class="{{ Route::is('admin.manufracturers') ? 'active' : '' }}">
                                    <a class="dropdown-item" href="{{ route('admin.manufracturers') }}"> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M2 22H22" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> <path d="M2.94995 22L2.99995 9.96999C2.99995 9.35999 3.28995 8.78004 3.76995 8.40004L10.77 2.95003C11.49 2.39003 12.4999 2.39003 13.2299 2.95003L20.23 8.39003C20.72 8.77003 21 9.34999 21 9.96999V22" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linejoin="round"/> <path d="M13 17H11C10.17 17 9.5 17.67 9.5 18.5V22H14.5V18.5C14.5 17.67 13.83 17 13 17Z" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linejoin="round"/> <path d="M9.5 13.75H7.5C6.95 13.75 6.5 13.3 6.5 12.75V11.25C6.5 10.7 6.95 10.25 7.5 10.25H9.5C10.05 10.25 10.5 10.7 10.5 11.25V12.75C10.5 13.3 10.05 13.75 9.5 13.75Z" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linejoin="round"/> <path d="M16.5 13.75H14.5C13.95 13.75 13.5 13.3 13.5 12.75V11.25C13.5 10.7 13.95 10.25 14.5 10.25H16.5C17.05 10.25 17.5 10.7 17.5 11.25V12.75C17.5 13.3 17.05 13.75 16.5 13.75Z" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linejoin="round"/> <path d="M19.0001 7L18.9701 4H14.5701" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> </svg> Plants </a>
                                </li>
                                <li class="{{ Route::is('admin.manufracturers.corporate') ? 'active' : '' }}">
                                    <a class="dropdown-item" href="{{ route('admin.manufracturers.corporate') }}">
                                    
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1 22H23" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M19.78 22.01V17.55" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M19.8001 10.89C18.5801 10.89 17.6001 11.87 17.6001 13.09V15.36C17.6001 16.58 18.5801 17.56 19.8001 17.56C21.0201 17.56 22.0001 16.58 22.0001 15.36V13.09C22.0001 11.87 21.0201 10.89 19.8001 10.89Z" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M2.1001 22V6.03003C2.1001 4.02003 3.10015 3.01001 5.09015 3.01001H11.3201C13.3101 3.01001 14.3001 4.02003 14.3001 6.03003V22" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M5.80005 8.25H10.7501" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M5.80005 12H10.7501" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M8.25 22V18.25" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
 Corporate</a>
                                </li>
                            </ul>
                        </li>

                        

                        <li class="nav-item {{ Route::is('admin.enquiries*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.enquiries') }}">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21.97 18.33C21.97 18.69 21.89 19.06 21.72 19.42C21.55 19.78 21.33 20.12 21.04 20.44C20.55 20.98 20.01 21.37 19.4 21.62C18.8 21.87 18.15 22 17.45 22C16.43 22 15.34 21.76 14.19 21.27C13.04 20.78 11.89 20.12 10.75 19.29C9.6 18.45 8.51 17.52 7.47 16.49C6.44 15.45 5.51 14.36 4.68 13.22C3.86 12.08 3.2 10.94 2.72 9.81C2.24 8.67 2 7.58 2 6.54C2 5.86 2.12 5.21 2.36 4.61C2.6 4 2.98 3.44 3.51 2.94C4.15 2.31 4.85 2 5.59 2C5.87 2 6.15 2.06 6.4 2.18C6.66 2.3 6.89 2.48 7.07 2.74L9.39 6.01C9.57 6.26 9.7 6.49 9.79 6.71C9.88 6.92 9.93 7.13 9.93 7.32C9.93 7.56 9.86 7.8 9.72 8.03C9.59 8.26 9.4 8.5 9.16 8.74L8.4 9.53C8.29 9.64 8.24 9.77 8.24 9.93C8.24 10.01 8.25 10.08 8.27 10.16C8.3 10.24 8.33 10.3 8.35 10.36C8.53 10.69 8.84 11.12 9.28 11.64C9.73 12.16 10.21 12.69 10.73 13.22C11.27 13.75 11.79 14.24 12.32 14.69C12.84 15.13 13.27 15.43 13.61 15.61C13.66 15.63 13.72 15.66 13.79 15.69C13.87 15.72 13.95 15.73 14.04 15.73C14.21 15.73 14.34 15.67 14.45 15.56L15.21 14.81C15.46 14.56 15.7 14.37 15.93 14.25C16.16 14.11 16.39 14.04 16.64 14.04C16.83 14.04 17.03 14.08 17.25 14.17C17.47 14.26 17.7 14.39 17.95 14.56L21.26 16.91C21.52 17.09 21.7 17.3 21.81 17.55C21.91 17.8 21.97 18.05 21.97 18.33Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" />
                                        <path
                                            d="M17.66 7.5V7.29004C17.66 6.61004 18.08 6.25002 18.5 5.96002C18.91 5.68002 19.3199 5.32003 19.3199 4.66003C19.3199 3.74003 18.58 3 17.66 3C16.74 3 16 3.74003 16 4.66003"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M17.6554 9.89014H17.6644" stroke="#5F0F58" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="menu-title">Inquiries</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Route::is('admin.settings*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.settings') }}">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 15.5C13.933 15.5 15.5 13.933 15.5 12C15.5 10.067 13.933 8.5 12 8.5C10.067 8.5 8.5 10.067 8.5 12C8.5 13.933 10.067 15.5 12 15.5Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M19.4 15.5L21 12L19.4 8.5L16.5 8.5L15 5.5L12 4L9 5.5L7.5 8.5L4.6 8.5L3 12L4.6 15.5L7.5 15.5L9 18.5L12 20L15 18.5L16.5 15.5L19.4 15.5Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="menu-title">Settings</span>
                            </a>
                        </li>



                        <li class="nav-item {{ Route::is('admin.profile*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.profile') }}">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.50998C8.71997 7.69998 10.18 6.22998 12 6.22998C13.81 6.22998 15.28 7.69998 15.28 9.50998C15.27 11.28 13.88 12.72 12.12 12.78Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M18.74 19.3801C16.96 21.0101 14.6 22.0001 12 22.0001C9.40001 22.0001 7.04001 21.0101 5.26001 19.3801C5.36001 18.4401 5.96001 17.5201 7.03001 16.8001C9.77001 14.9801 14.25 14.9801 16.97 16.8001C18.04 17.5201 18.64 18.4401 18.74 19.3801Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="menu-title">Admin Profile</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.logout') }}">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.4399 14.62L19.9999 12.06L17.4399 9.5" stroke="#5F0F58"
                                            stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M9.76001 12.0601H19.93" stroke="#5F0F58" stroke-width="1.5"
                                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M11.76 20C7.34001 20 3.76001 17 3.76001 12C3.76001 7 7.34001 4 11.76 4"
                                            stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="menu-title">Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="body-wrapper">
            <div class="header">
                <nav class="navbar">
                    <div class="navbar-menu-wrapper">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link toggle-sidebar mon-icon-bg">
                                    <img src="{{ asset('images/sidebartoggle.svg') }}">
                                </a>
                            </li>
                        </ul>
                        <ul class="navbar-nav">
                            <!-- <li class="nav-item noti-dropdown dropdown">
                                <a class="nav-link  dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="noti-icon">
                                        <img src="images/notification.svg" alt="user">
                                        <span class="noti-badge"></span>
                                    </div>
                                </a>

                                <div class="dropdown-menu">
                                   
                                </div>
                            </li> -->
                            <li class="nav-item profile-dropdown dropdown">
                                <a class="nav-link dropdown-toggle" id="profile" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <div class="profile-dropdown-head-info">
                                        <div class="profile-pic"><img
                                                src="{{ auth('admin')->user()->profile_image ? asset(auth('admin')->user()->profile_image) : asset('admin/images/profile.png') }}"
                                                alt="user"> </div>
                                        <div class="profile-text">
                                            <h3>{{ auth('admin')->user()->fullname }}</h3>
                                            <p> Admin</p>
                                        </div>
                                    </div>
                                </a>

                                <div class="dropdown-menu" style=" border-top: 1px solid var(--border); padding-top: 10px; gap: 10px;">
                                    <a href="{{route('admin.profile')}}" class="dropdown-item" style="font-size: 15px;">
                                    <i class="las la-user" style="font-size: 18px;"></i> Profile
                                    </a>
                                    <a href="{{ route('admin.logout') }}" class="dropdown-item" style="font-size: 15px;">
                                       <i class="las la-sign-out-alt" style="font-size: 18px;"></i> Logout
                                    </a>
                                </div>
                            </li>

                        </ul>

                        </ul>
                    </div>
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                        data-toggle="offcanvas">
                        <span class="icon-menu"></span>
                    </button>
                </nav>
            </div>

            @yield('content')

        </div>
    </div>
    <script>
        $("#date").datepicker({
            dateFormat: 'mm-dd-yy',
            maxDate: 0, // Maximum selectable date is today
            changeMonth: true,
            changeYear: true,
            yearRange: 'c-100:c', // Allow selection of the past 100 years
            onSelect: function(dateText) {
                $(this).val(dateText);

                var dateParts = dateText.split('-');
                var selectedValue = dateParts[2] + '-' + dateParts[0] + '-' + dateParts[1];

                var currentUrl = new URL(window.location.href);
                // Add or update the 'run_id' parameter
                currentUrl.searchParams.set('date', selectedValue);
                if (selectedValue == "") {
                    currentUrl.searchParams.delete('date');

                }
                // Reload the page with the new URL
                window.location.href = currentUrl.toString();
            }
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
