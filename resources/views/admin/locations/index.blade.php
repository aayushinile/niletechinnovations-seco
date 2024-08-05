
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Show Search</title>
    <link rel="stylesheet" type="text/css" href="css/header-footer.css">
    <link rel="stylesheet" type="text/css" href="css/managelocations.css">
	<script src="js/jquery-3.7.1.min.js" type="text/javascript"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="js/function.js" type="text/javascript"></script>
</head>
<body class="main-site">
    <div class="page-body-wrapper">
        <div class="sidebar-wrapper">
            <div class="sidebar-logo">
                <a href="index.html">
                    <img class="" src="images/logo.svg" alt="">
                </a>
              <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            </div>
            <div class="sidebar-nav">
                <nav class="sidebar sidebar-offcanvas" id="sidebar">
                    <ul class="nav">
                        <li class="nav-item ">
                            <a class="nav-link" href="index.html">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M9.02 2.83992L3.63 7.03992C2.73 7.73992 2 9.22992 2 10.3599V17.7699C2 20.0899 3.89 21.9899 6.21 21.9899H17.79C20.11 21.9899 22 20.0899 22 17.7799V10.4999C22 9.28992 21.19 7.73992 20.2 7.04992L14.02 2.71992C12.62 1.73992 10.37 1.78992 9.02 2.83992Z" stroke="#5f0f58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M16.5 11.5L12.3 15.7L10.7 13.3L7.5 16.5" stroke="#5f0f58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M14.5 11.5H16.5V13.5" stroke="#5f0f58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>
                                </span>
                                <span class="menu-title">Dashboard</span>
                            </a>

                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="enquiries.html">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M21.97 18.33C21.97 18.69 21.89 19.06 21.72 19.42C21.55 19.78 21.33 20.12 21.04 20.44C20.55 20.98 20.01 21.37 19.4 21.62C18.8 21.87 18.15 22 17.45 22C16.43 22 15.34 21.76 14.19 21.27C13.04 20.78 11.89 20.12 10.75 19.29C9.6 18.45 8.51 17.52 7.47 16.49C6.44 15.45 5.51 14.36 4.68 13.22C3.86 12.08 3.2 10.94 2.72 9.81C2.24 8.67 2 7.58 2 6.54C2 5.86 2.12 5.21 2.36 4.61C2.6 4 2.98 3.44 3.51 2.94C4.15 2.31 4.85 2 5.59 2C5.87 2 6.15 2.06 6.4 2.18C6.66 2.3 6.89 2.48 7.07 2.74L9.39 6.01C9.57 6.26 9.7 6.49 9.79 6.71C9.88 6.92 9.93 7.13 9.93 7.32C9.93 7.56 9.86 7.8 9.72 8.03C9.59 8.26 9.4 8.5 9.16 8.74L8.4 9.53C8.29 9.64 8.24 9.77 8.24 9.93C8.24 10.01 8.25 10.08 8.27 10.16C8.3 10.24 8.33 10.3 8.35 10.36C8.53 10.69 8.84 11.12 9.28 11.64C9.73 12.16 10.21 12.69 10.73 13.22C11.27 13.75 11.79 14.24 12.32 14.69C12.84 15.13 13.27 15.43 13.61 15.61C13.66 15.63 13.72 15.66 13.79 15.69C13.87 15.72 13.95 15.73 14.04 15.73C14.21 15.73 14.34 15.67 14.45 15.56L15.21 14.81C15.46 14.56 15.7 14.37 15.93 14.25C16.16 14.11 16.39 14.04 16.64 14.04C16.83 14.04 17.03 14.08 17.25 14.17C17.47 14.26 17.7 14.39 17.95 14.56L21.26 16.91C21.52 17.09 21.7 17.3 21.81 17.55C21.91 17.8 21.97 18.05 21.97 18.33Z" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10"/> <path d="M17.66 7.5V7.29004C17.66 6.61004 18.08 6.25002 18.5 5.96002C18.91 5.68002 19.3199 5.32003 19.3199 4.66003C19.3199 3.74003 18.58 3 17.66 3C16.74 3 16 3.74003 16 4.66003" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M17.6554 9.89014H17.6644" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>
                                </span>
                                <span class="menu-title">Enquiries</span>
                            </a>
                        </li>
                        
                        <li class="nav-item active">
                            <a class="nav-link" href="managelocation.html">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M22 12C22 6.48 17.52 2 12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8.0001 3H9.0001C7.0501 8.84 7.0501 15.16 9.0001 21H8.0001" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M15 3C15.97 5.92 16.46 8.96 16.46 12" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M3 16V15C5.92 15.97 8.96 16.46 12 16.46" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M3 8.99998C8.84 7.04998 15.16 7.04998 21 8.99998" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M19.21 15.74L15.67 19.2801C15.53 19.4201 15.4 19.68 15.37 19.87L15.18 21.22C15.11 21.71 15.45 22.05 15.94 21.98L17.29 21.79C17.48 21.76 17.75 21.63 17.88 21.49L21.42 17.95C22.03 17.34 22.32 16.63 21.42 15.73C20.53 14.84 19.82 15.13 19.21 15.74Z" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> <path d="M18.7 16.25C19 17.33 19.84 18.17 20.92 18.47" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> </svg>
                                </span>
                                <span class="menu-title">Manage Location & Listing</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="profile.html">
                                <span class="menu-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.50998C8.71997 7.69998 10.18 6.22998 12 6.22998C13.81 6.22998 15.28 7.69998 15.28 9.50998C15.27 11.28 13.88 12.72 12.12 12.78Z" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M18.74 19.3801C16.96 21.0101 14.6 22.0001 12 22.0001C9.40001 22.0001 7.04001 21.0101 5.26001 19.3801C5.36001 18.4401 5.96001 17.5201 7.03001 16.8001C9.77001 14.9801 14.25 14.9801 16.97 16.8001C18.04 17.5201 18.64 18.4401 18.74 19.3801Z" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#5F0F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>
                                </span>
                                <span class="menu-title">Manufacturer Profile</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="login.html">
                                <span class="menu-icon">
                                   <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M17.4399 14.62L19.9999 12.06L17.4399 9.5" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> <path d="M9.76001 12.0601H19.93" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> <path d="M11.76 20C7.34001 20 3.76001 17 3.76001 12C3.76001 7 7.34001 4 11.76 4" stroke="#5F0F58" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> </svg>
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
                                	<img src="images/sidebartoggle.svg" >   
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
                                <a class="nav-link dropdown-toggle" id="profile" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="profile-dropdown-head-info">
                                        <div  class="profile-pic"><img src="images/images.jpg" alt="user"> </div>
                                        <div  class="profile-text">
                                            <h3>Palm Harbor Homes</h3>
                                            <p><img src="images/verified.svg"> Verified</p>
                                        </div>
                                    </div>
                                </a>

                                <div class="dropdown-menu">
                                    <a href="#" class="dropdown-item">
                                        <i class="las la-user"></i> Profile
                                    </a>
                                    <a href="#" class="dropdown-item">
                                       <i class="las la-sign-out-alt"></i> Logout
                                    </a>
                                </div>
                            </li>

                        </ul>

                        </ul>
                    </div>
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                        <span class="icon-menu"></span>
                    </button>
                </nav>
            </div>

            <div class="body-main-content">
                <div class="ss-heading-section">
                    <h2>Manage Location & Listing</h2>
                    <div class="search-filter wd40">
                        <div class="row g-1">
                            <div class="col-md-8">
                                <div class="form-group search-form-group">
                                    <input type="text" name="" class="form-control" placeholder="Search">
                                    <span class="search-icon"><img src="images/search-icon.svg"></span>
                                </div> 
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <a href="addnewplants.html" class="addnewplant-btn">Add New Plant</a>
                                </div> 
                            </div>
                        </div>   
                    </div>
                </div>
                <div class="listed-plants-section">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="listed-plants-item">
                                <div class="listed-plants-item-head">
                                    <div class="listed-plants-item-profile">
                                        <div class="listed-plants-item-image">
                                            <img src="images/images.jpg">
                                        </div>
                                        <div class="listed-plants-item-content">
                                            <h2>Palm Harbor Homes 1</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="listed-plants-item-media">
                                    <img src="images/1.jpg">
                                </div> 
                                <div class="listed-plants-item-descr">
                                    <div class="listed-plants-location"><img src="images/location.svg">Atlanta, GA</div>
                                    <div class="listed-plants-item-action">
                                       <a class="deletebtn" data-bs-toggle="modal" data-bs-target="#deleteplants">Delete</a>
                                       <a class="editbtn" href="">Edit</a>
                                       <a class="viewbtn" href="managelocation-details.html">View</a>
                                    </div> 
                                </div>
                                
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="listed-plants-item">
                                <div class="listed-plants-item-head">
                                    <div class="listed-plants-item-profile">
                                        <div class="listed-plants-item-image">
                                            <img src="images/images.jpg">
                                        </div>
                                        <div class="listed-plants-item-content">
                                            <h2>Palm Harbor Homes 2</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="listed-plants-item-media">
                                    <img src="images/1.jpg">
                                </div> 
                                <div class="listed-plants-item-descr">
                                    <div class="listed-plants-location"><img src="images/location.svg">Emerson, GA</div>
                                    <div class="listed-plants-item-action">
                                       <a class="deletebtn" data-bs-toggle="modal" data-bs-target="#deleteplants">Delete</a>
                                       <a class="editbtn" href="">Edit</a>
                                       <a class="viewbtn" href="managelocation-details.html">View</a>
                                    </div> 
                                </div>
                                
                            </div>
                        </div>

                         <div class="col-md-4">
                            <div class="listed-plants-item">
                                <div class="listed-plants-item-head">
                                    <div class="listed-plants-item-profile">
                                        <div class="listed-plants-item-image">
                                            <img src="images/images.jpg">
                                        </div>
                                        <div class="listed-plants-item-content">
                                            <h2>Palm Harbor Homes 3</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="listed-plants-item-media">
                                    <img src="images/1.jpg">
                                </div> 
                                <div class="listed-plants-item-descr">
                                    <div class="listed-plants-location"><img src="images/location.svg">Emerson, GA</div>
                                    <div class="listed-plants-item-action">
                                       <a class="deletebtn" data-bs-toggle="modal" data-bs-target="#deleteplants">Delete</a>
                                       <a class="editbtn" href="">Edit</a>
                                       <a class="viewbtn" href="managelocation-details.html">View</a>
                                    </div> 
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>



<!-- Delete listed plants -->
<div class="modal ss-modal fade" id="deleteplants" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="ss-modal-delete">
            <div class="ss-modal-delete-icon"><img src="images/delete.svg"></div>
            <h2>Delete listed plants</h2>
            <p>Are you sure want to delete “Palm Harbor Homes 1” from listing?</p>
            <div class="ss-modal-delete-action">
                <button class="yes-btn" >Yes, Delete</button>
                <button class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>