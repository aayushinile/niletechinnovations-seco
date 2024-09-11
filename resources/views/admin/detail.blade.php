@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/manufacturers.css') }}">
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-heading-section">
            <h2>Manufacturer Details</h2>
        </div>
        <div class="listed-plants-section">
            <div class="plants-details-head" style="justify-content:end;">
                <div class="plants-details-action">
                    @if ($mfs->status == 1)
                        <a class="edit-btn" style="background: var(--red);" data-bs-toggle="modal"
                            data-bs-target="#inactivePlant" data-plant-id="{{ $mfs['id'] }}"> Mark As Approve</a>
                    @else
                        <a class="" data-bs-toggle="modal" data-bs-target="#activePlant"
                            data-plant-id="{{ $mfs['id'] }}"
                            style="background: var(--green);color: var(--white);padding: 12px 20px;border-radius: 5px;font-size: 14px;box-shadow: 0 4px 10px #5f0f5845;display: inline-block;position: relative;">UnApprove</a>
                    @endif
                </div>
            </div>
            <div class="user-table-item">
                <div class="row g-1 align-items-center">
                    <div class="col-md-4">
                        <div class="user-profile-item">
                            <div class="user-profile-media">
                                <img src="{{ asset('images/default-user-2.png') }}">
                            </div>
                            <div class="user-profile-text">
                                <h2>{{ $manufacturer->full_name ?? 'N/A' }}</h2>
                                <div class="status-text {{ $mfs->status == 1 ? 'status-active' : 'status-inactive' }}">
                                    {{ $mfs->status == 1 ? 'Approved' : 'Pending' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-1 align-items-center">
                            <div class="col-md-4">
                                <div class="user-contact-info">
                                    <div class="user-contact-info-icon">
                                        <img src="{{ asset('admin/images/location.svg') }}">
                                    </div>
                                    <div class="user-contact-info-content">
                                        <h2>Location</h2>
                                        <p>{{ $manufacturer->manufacturer_address ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="user-contact-info">
                                    <div class="user-contact-info-icon">
                                        <img src="{{ asset('admin/images/sms.svg') }}">
                                    </div>
                                    <div class="user-contact-info-content">
                                        <h2>Email</h2>
                                        <p>{{ $manufacturer->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="user-contact-info">
                                    <div class="user-contact-info-icon">
                                        <img src="{{ asset('admin/images/call.svg') }}">
                                    </div>
                                    <div class="user-contact-info-content">
                                        <h2>Phone</h2>
                                        <p>{{ $manufacturer->mobile ? '+1 ' . $manufacturer->mobile : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="listed-plants-section">
                <div class="plants-details-head">
                    <div class="plants-details-item">
                        <div class="plants-details-logo">
                            @if (!empty($images))
                                @foreach ($images as $image)
                                    <img src="{{ asset('upload/manufacturer-image/' . $image['image_url']) }}">
                                @endforeach
                            @else
                                <img src="{{ asset('images/default-user-2.png') }}">
                            @endif
                        </div>
                        <div class="plants-details-head-text">

                            <h4>{{ $plant['plant_name'] ?? 'N/A' }}</h4>
                            <div class="plants-details-location">
                                <img src="{{ asset('images/location-icon.svg') }}">{{ $plant['full_address'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>


                </div>
                <div class="listed-plants-details-section">
                    <div id="plants-slider" class="owl-carousel owl-theme">
                        @foreach ($images as $image)
                            <div class="item">
                                <div class="listed-plants-slider-media">
                                    <a href="{{ asset('upload/manufacturer-image/' . $image['image_url']) }}"
                                        data-fancybox="gallery">
                                        <img src="{{ asset('upload/manufacturer-image/' . $image['image_url']) }}" />
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if (!empty($plant['description']))
                    <div class="plants-about-section">
                        <h2>About Us</h2>
                        <p>{!! nl2br(e($plant['description'])) !!}</p>
                    </div>
                @else
                    <div class="plants-about-section">
                        <h2>About Us</h2>
                        <p>N/A</p>
                    </div>
                @endif

                <div class="amenities-section">
                    <h4 style="color: var(--pink);">Specifications</h4>
                    <div class="row">
                        @if (!empty($specifications))
                            @foreach ($specifications as $specification)
                                <div class="col-md-2">
                                    <div class="plants-amenities-info"
                                        style="position: relative;width: 100%;border-radius: 0;padding: 0;display: flex;gap: 10px;margin-bottom: 0rem;">

                                        <div class="plants-amenities-info-content"
                                            style="display: flex;gap: 10px;align-items: center;border: 1px solid;padding: 8px;width: 100%;border-radius: 5px;background: #fff; height: 50px;">
                                            @if (!empty($specification->image))
                                                <div class="plants-amenities-info-icon">
                                                    <img
                                                        src="{{ asset('upload/specification-image/' . $specification->image) }}">
                                                </div>
                                            @endif
                                            <h2 style="margin-top: 4px;">{{ $specification->name }}</h2>
                                            <p style="margin-top: 3px;">{{ $specification->values }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-2">
                                <div class="plants-amenities-info">
                                    <div class="plants-amenities-info-content">
                                        <h2>Specifications</h2>
                                        <p>N/A</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-2">
                            @if (!empty($plant['type']))
                                <div class="plants-amenities-info"
                                    style="position: relative;width: 100%;border-radius: 0;padding: 0;display: flex;gap: 10px;margin-bottom: 0rem;">
                                    <div class="plants-amenities-info-content"
                                        style="display: flex;gap: 10px;align-items: center;border: 1px solid;padding: 8px;width: 100%;border-radius: 5px;background: #fff; height:50px">

                                        <h2 style="margin-top: 4px;">Type</h2>
                                        @if ($plant['type'] == 'sw')
                                            <p style="margin-top: 3px;">Single Wide</p>
                                        @elseif ($plant['type'] == 'dw')
                                            <p style="margin-top: 3px;">Double Wide</p>
                                        @else
                                            <p>Single Wide & Double Wide</p>
                                        @endif

                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>


                </div>

                <div class="pricing-host-section">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pricing-host-box">
                                <div class="pricing-info">
                                    <div class="plants-pricing-item">
                                        <h2>Price Starting @ ${{ $plant['price_range'] ?? 'N/A' }}</h2>
                                    </div>
                                </div>
                                <div class="contact-item-info">
                                    <img
                                        src="{{ asset('images/call.svg') }}">{{ $plant && $plant['phone'] ? '+1' . $plant['phone'] : 'N/A' }}
                                </div>

                                <div class="contact-item-info">
                                    <img src="{{ asset('images/sms.svg') }}"> {{ $plant['email'] ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="sales-manager-info">
                                <h1>Our Sales Managers</h1>
                                <div class="row">
                                    @foreach ($sales_managers as $manager)
                                        <div class="col-md-4">
                                            <div class="sales-manager-card">
                                                <div class="sales-manager-image">
                                                    @if (!empty($manager['image']))
                                                        <img
                                                            src="{{ asset('upload/sales-manager-images/' . $manager['image']) }}">
                                                    @else
                                                        <img src="{{ asset('images/profile.png') }}">
                                                    @endif
                                                </div>
                                                <div class="sales-manager-content">
                                                    <h3>{{ $manager['name'] }}</h3>
                                                    <h4>{{ $manager['designation'] }}</h4>
                                                    <div class="sales-manager-contact">
                                                        <img src="{{ asset('images/call.svg') }}">
                                                        +1{{ $manager['phone'] }}
                                                    </div>
                                                    <div class="sales-manager-contact">
                                                        <img src="{{ asset('images/sms.svg') }}"> {{ $manager['email'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if (empty($sales_managers))
                                        <p>No sales managers available</p>
                                    @endif
                                </div>
                            </div>
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
                            <div class="ss-modal-delete-icon"><img src=""></div>
                            <p id="delete-message">Are you sure you want to activate this Plant?</p>
                            <form id="activate-form" method="POST">
                                @csrf
                                <input type="hidden" id="plant-id" name="plant_id">
                                <div class="ss-modal-delete-action">
                                    <button type="button" id="confirm-activate" class="yes-btn">Yes, Confirm</button>
                                    <button type="button" class="cancel-btn" data-bs-dismiss="modal"
                                        aria-label="Close">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal ss-modal fade" id="inactivePlant" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="ss-modal-delete">
                            <div class="ss-modal-delete-icon"><img src=""></div>
                            <p id="delete-message">Are you sure you want to In-activate this Plant?</p>
                            <form id="inactivate-form" method="POST">
                                @csrf
                                <input type="hidden" id="plant-id" name="plant_id">
                                <div class="ss-modal-delete-action">
                                    <button type="button" id="confirm-inactivate" class="yes-btn">Yes, Confirm</button>
                                    <button type="button" class="cancel-btn" data-bs-dismiss="modal"
                                        aria-label="Close">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="loader-container" id="loader">
                <div class="loader"></div>
            </div>

        <script>
            $(document).ready(function() {
                // Set plant ID in hidden input field when the modal is triggered
                $('[data-bs-toggle="modal"]').on('click', function() {
                    var plantId = $(this).data('plant-id');
                    console.log(plantId);
                    $('#plant-id').val(plantId);
                });

                // Handle form submission via AJAX
                $('#confirm-activate').on('click', function() {
                    var plantId = $('#plant-id').val();
                    $.ajax({
                        url: '{{ route('admin.activate.plant') }}', // Adjust the URL to match your route
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            plant_id: plantId
                        },
                        success: function(response) {
                            // Close the modal
                            $('#activePlant').modal('hide');
                            // Reload the page
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('An error occurred while activating the plant.');
                        }
                    });
                });


                $('#confirm-inactivate').on('click', function() {
                    var plantId = $('#plant-id').val();
                    $.ajax({
                        url: '{{ route('admin.inactivate.plant') }}', // Adjust the URL to match your route
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            plant_id: plantId
                        },
                        success: function(response) {
                            // Close the modal
                            $('#inactivePlant').modal('hide');
                            // Reload the page
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('An error occurred while activating the plant.');
                        }
                    });
                });
            });
        </script>
    @endsection
