
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Show Search</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/contactus.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') }}">
	<script src="{{asset('js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
 <div class="contactus-page-section">
    <div class="container">
        <div class="contactus-page-content">
                
            <div class="row">
                <div class="col-md-6">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="contact-form-card">
                    
                    <h4>Send Us A Message</h4>
                    <p>Our team is committed to supporting you and your clients, providing guidance and expertise every step of the way.</p>
                    <div class="contact-form">
                        <form action="{{ route('contact.save') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input id="first_name" class="form-control" name="first_name" type="text" placeholder="First Name">  
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input id="last_name" class="form-control" name="last_name" type="text" placeholder="Last Name">  
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input id="email" class="form-control" name="email" required="" type="email" placeholder="Email Address *">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input id="phone" class="form-control" name="phone" type="text" placeholder="Phone" id="phone">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <textarea id="message" class="form-control" name="message" rows="2" placeholder="Message"></textarea>
                                    </div>
                                </div>
                                <p class="mt-2 mb-2">We are committed to your privacy. Do not include confidential or private information in this form. This form is for general questions or messages.</p>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" class="contact-fill-btn">SUBMIT</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
                <div class="col-md-6">
                    <div class="contact-googlemap-card">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d224357.50123586552!2d77.23701468919643!3d28.5221023514615!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce5a43173357b%3A0x37ffce30c87cc03f!2sNoida%2C%20Uttar%20Pradesh!5e0!3m2!1sen!2sin!4v1725435045208!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>    
                    </div>
                </div>
            </div>
        </div>    
    </div>
 </div> 
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
 <script type="text/javascript">
    $(document).ready(function(){
        $('#phone').mask('(000) 000-0000');
    });
</script> 
</body>
</html>

