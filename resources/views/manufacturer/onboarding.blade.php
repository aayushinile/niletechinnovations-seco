
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Show Search</title>
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/OwlCarousel/assets/owl.carousel.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/onboarding.css')}}">
	<script src="{{asset('js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/OwlCarousel/owl.carousel.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/function.js')}}" type="text/javascript"></script>
</head>
<body>
<div class="onboarding-section">
    <div class="onboarding-section-inner">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="logo"><img src="{{asset('images/logo.svg')}}"></div>
                    <div class="onboardings-slider">
                        <div id="onboardingslider" class="owl-carousel owl-theme">
                            <div class="item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-item">
                                            <div class="onboarding-slider-media">
                                                <img src="{{asset('images/onboarding5.png')}}">
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-text">
                                                <h2>Sign Up</h2>
                                                <p>This Free App Allows You To Enter Information About Your Plants And Homes So Community Owners Attending This Show Can Quickly Identify Plants Which Can Supply Homes To Their Community, Considering Transportation/Shipping Costs And Home Features & Options</p>
                                            </div>
                                    </div>
                                </div>
                            </div>

                            <div class="item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-item">
                                            <div class="onboarding-slider-media">
                                                <img src="{{asset('images/onboarding1.png')}}">
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-text">
                                                <h2>Dashboard</h2>
                                                <p>This Free App Allows You To Enter Information About Your Plants And Homes So Community Owners Attending This Show Can Quickly Identify Plants Which Can Supply Homes To Their Community, Considering Transportation/Shipping Costs And Home Features & Options</p>
                                            </div>
                                    </div>
                                </div>
                            </div>

                            <div class="item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-item">
                                            <div class="onboarding-slider-media">
                                                <img src="{{asset('images/onboarding2.png')}}">
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-text">
                                               <h2>Enquiries</h2>
                                                <p>Get Community Owner Enquiries Details Straight To This Free App</p>
                                            </div>
                                    </div>
                                </div>
                            </div>


                            <div class="item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-item">
                                            <div class="onboarding-slider-media">
                                                <img src="{{asset('images/onboarding3.png')}}">
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-text">
                                                <h2>Manage Location & Listing</h2>
                                                <p>To Save Your Plant Information, To Add More Information About Your Plant And Homes, And To Use This Information At Other Shows, Please Create An Account Within This App</p>
                                            </div>
                                    </div>
                                </div>
                            </div>


                            <div class="item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-item">
                                            <div class="onboarding-slider-media">
                                                <img src="{{asset('images/onboarding4.png')}}">
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="onboarding-slider-text">
                                            <h2>Plant Detail</h2>
                                            <p>This App Also Allows Interested Community Owners To Easily Contact Your Sales Personnel Here At The Show And To Share Their Contact Information.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="item">
                                <div class="onboarding-slider-item">
                                    <div class="onboarding-slider-media">
                                        <img src="images/onboarding2.png">
                                    </div>
                                    <div class="onboarding-slider-text">
                                        <h2>Enquiries</h2>
                                        <p>Get Community Owner Enquiries Details Straight To This Free App</p>
                                    </div>
                                </div>
                            </div>

                            <div class="item">
                                <div class="onboarding-slider-item">
                                    <div class="onboarding-slider-media">
                                        <img src="images/onboarding3.png">
                                    </div>
                                    <div class="onboarding-slider-text">
                                        <h2>Manage Location & Listing</h2>
                                        <p>To Save Your Plant Information, To Add More Information About Your Plant And Homes, And To Use This Information At Other Shows, Please Create An Account Within This App</p>
                                    </div>
                                </div>
                            </div>

                            <div class="item">
                                <div class="onboarding-slider-item">
                                    <div class="onboarding-slider-media">
                                        <img src="images/onboarding4.png">
                                    </div>
                                    <div class="onboarding-slider-text">
                                        <h2>Plant Detail</h2>
                                        <p>This App Also Allows Interested Community Owners To Easily Contact Your Sales Personnel Here At The Show And To Share Their Contact Information.</p>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="onboarding-slider-action">
                        <a class="Skipbtn" href="{{ route('signup') }}">Create an Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
</body>
</html>

