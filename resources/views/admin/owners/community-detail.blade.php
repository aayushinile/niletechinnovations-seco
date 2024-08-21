@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/plants-details.css') }}">
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-heading-section">
            <h2>Community Owner/Retailer</h2>
        </div>
        <div class="listed-plants-section">
            <div class="plants-details-head">
                <div class="plants-details-item">
                    <div class="plants-details-logo"><img src="{{ asset('images/defaultuser.png') }}"></div>
                    <div class="plants-details-head-text">
                        <h4>{{ $community->community_name }} </h4>
                        <div class="plants-details-location">
                            <img src="{{ asset('admin/images/location-icon.svg') }}"> {{ $community->community_address }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="listed-plants-details-section">
                <div id="plants-slider" class="owl-carousel owl-theme">
                    @foreach ($images as $item)
                        <div class="item">
                            <div class="listed-plants-slider-media">
                                <a href="{{  asset('upload/community_image/' . $item) }}" data-fancybox="gallery">
                                    <img src="{{  asset('upload/community_image/' . $item) }}" />
                                </a>
                            </div>
                        </div>
                    @endforeach


                </div>
            </div>
            <div class="plants-about-section">
                <h2>Details</h2>
                <p>{{ $community->description }}</p>
            </div>



            <div class="pricing-host-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="pricing-info">
                            <!-- <div class="plants-pricing-item">
                                                                                                                                                                                                                                                                        <h2>Price Starting @ $215,000</h2>
                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                     <div class="plants-pricing-item">
                                                                                                                                                                                                                                                                        <h2>Shipping Cost @ $21(Rate Per Miles)</h2>
                                                                                                                                                                                                                                                                    </div>  -->
                        </div>
                        <div class="contact-item-info">
                            <img src="{{ asset('admin/images/call.svg') }}"> +1 {{ $community->mobile }}
                        </div>
                        <div class="contact-item-info">
                            <img src="{{ asset('admin/images/sms.svg') }}"> {{ $community->email }}
                        </div>
                        <div class="community-item-card">
                            <div class="community-item-info">
                                <div class="community-item-text">No.of Lots: </div>
                                <div class="community-item-value">{{ $community->no_of_lots ?? 'N/A' }}</div>
                            </div>
                            <div class="community-item-info">
                                <div class="community-item-text">No.of New Homes:</div>
                                <div class="community-item-value"> {{ $community->no_of_new_homes ?? 'N/A' }}</div>
                            </div>
                            <div class="community-item-info">
                                <div class="community-item-text">No.of Vacant Lots:</div> 
                                <div class="community-item-value">{{ $community->vacant_lots ?? 'N/A' }}</div> 
                            </div>
                            <div class="community-item-info">
                                <div class="community-item-text">No.of Home Needed:</div> 
                                <div class="community-item-value">{{ $community->no_of_home_needed ?? 'N/A' }}</div> 
                            </div>
                        </div>
                        {{-- <div class="map-item-info">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3325.8099494628664!2d-85.06947302568288!3d33.53232654500683!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x888b2fcd1c4804d5%3A0x23008d9af9de2df7!2sCedar%20Village%20Manufactured%20Housing!5e0!3m2!1sen!2sin!4v1721107499490!5m2!1sen!2sin"
                                width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div> --}}
                    </div>

                    <div class="col-md-6">
                        <div class="sales-manager-info">
                            <h1>Our Team Members</h1>
                            @if($managers->isEmpty())
                            <div class="sales-manager-card">
                                    <div class="sales-manager-content">
                                        <h4>No Member added</h4>
                                    </div>
                                </div>
                            @else 

                            @foreach ($managers as $item)
                                <div class="sales-manager-card">
                                    <div class="sales-manager-image">
                                        <img src="{{ asset('admin/images/profile.png') }}">
                                    </div>
                                    <div class="sales-manager-content">
                                        <h3>{{ $item->name }}</h3>
                                        <h4>{{ $item->designation }}</h4>
                                        <div class="sales-manager-contact">
                                            <img src="{{ asset('admin/images/call.svg') }}"> +1 {{ $item->phone }}
                                        </div>

                                        <div class="sales-manager-contact">
                                            <img src="{{ asset('admin/images/sms.svg') }}">
                                            {{ $item->email_id }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @endif



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete listed plants -->
    <div class="modal ss-modal fade" id="deleteplants" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-delete">
                        <div class="ss-modal-delete-icon"><img src="{{ asset('admin/images/delete.svg') }}"></div>
                        <h2>Delete listed plants</h2>
                        <p>Are you sure want to delete “Palm Harbor Homes 1” from listing?</p>
                        <div class="ss-modal-delete-action">
                            <button class="yes-btn">Yes, Delete</button>
                            <button class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
