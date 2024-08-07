@extends('manufacturer.layouts')
@section('content')

    <div class="body-main-content">
        <div class="ss-heading-section">
            <h2>Manage Plant/Manufacturer Details</h2>
        </div>
        <div class="listed-plants-section">
            <div class="plants-details-head">
                <div class="plants-details-item">
                    <div class="plants-details-logo">
                        @if (!empty($plant['images']))
                            @foreach ($plant['images'] as $image)
                                <img src="{{ asset('upload/manufacturer-image/' . $image['image_url']) }}">
                            @endforeach
                        @else
                            <img src="{{ asset('images/defaultuser.png') }}">
                        @endif
                    </div>
                    <div class="plants-details-head-text">

                        <h4>{{ $plant['plant_name'] }}</h4>
                        <div class="plants-details-location">
                            <img src="{{ asset('images/location-icon.svg') }}">{{ $plant['full_address'] }}
                        </div>
                    </div>
                </div>
                <div class="plants-details-action">
                    <a class="edit-btn" href="{{ url('edit-plant/' . $plant['id']) }}"> Edit Details</a>
                    <a class="" data-bs-toggle="modal" data-bs-target="#deleteplants"
                        data-plant-id="{{ $plant['id'] }}" data-plant-name="{{ $plant['plant_name'] }}"
                        style="background: var(--red);color: var(--white);padding: 12px 20px;border-radius: 5px;font-size: 14px;box-shadow: 0 4px 10px #5f0f5845;display: inline-block;position: relative;">Delete</a>
                </div>
            </div>
            <div class="listed-plants-details-section">
                <div id="plants-slider" class="owl-carousel owl-theme">
                    @foreach ($plant['images'] as $image)
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
            <div class="plants-about-section">
                <h2>About Us</h2>
                <p>{!! nl2br(e($plant['description'])) !!}</p>
            </div>

            <div class="amenities-section">
                <h4 style="color: var(--pink);">Specifications</h4>
                <div class="row">
                    @if (!empty($plant['specifications']))
                        @foreach ($plant['specifications'] as $specification)
                            <div class="col-md-3">
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
                                        <h2 style="margin-top: 4px;">{{ $specification->name }} : </h2>
                                        <p style="margin-top: 3px;">{{ $specification->values }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-md-3">
                            <div class="plants-amenities-info">
                                <div class="plants-amenities-info-content">
                                    <h2>Specifications</h2>
                                    <p>No specifications available</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="plants-amenities-info"
                            style="position: relative;width: 100%;border-radius: 0;padding: 0;display: flex;gap: 10px;margin-bottom: 0rem;">
                            <div class="plants-amenities-info-content"
                                style="display: flex;gap: 10px;align-items: center;border: 1px solid;padding: 8px;width: 100%;border-radius: 5px;background: #fff; height:50px">
                                <h2 style="margin-top: 4px;">Type :</h2>
                                @if ($plant['type'] == 'sw')
                                    <p style="margin-top: 3px;">Single Wide</p>
                                @elseif ($plant['type'] == 'dw')
                                    <p style="margin-top: 3px;">Double Wide</p>
                                @else
                                    <p>Single Wide & Double Wide</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="pricing-host-section">
                <div class="row">
                    <div class="col-md-12">
                        <div class="pricing-host-box">
                            <div class="pricing-info">
                                <div class="plants-pricing-item">
                                @if (!empty($plant['price_range']))
                                    <h2>Price Range : ${{ $plant['price_range'] }}</h2>
                                @else
                                    <h2>Price Range : ${{ $plant['from_price_range'] }} - ${{ $plant['to_price_range'] }}</h2>
                                @endif
                                </div>
                                @php
                                    $shippingCost = DB::table('shipping_cost')
                                        ->where('type', $plant['type'])
                                        ->first();
                                @endphp
                                @if ($shippingCost)
                                    <div class="plants-pricing-item">
                                        <h2>Shipping Cost Per Mile @ ${{ $shippingCost->shipping_cost }}</h2>
                                    </div>
                                @endif
                            </div>
                            <div class="contact-item-info">
                                <img src="{{ asset('images/call.svg') }}"> {{ $plant['phone'] ? '+1 ' . $plant['phone'] : 'N/A' }}
                            </div>

                            <div class="contact-item-info">
                                <img src="{{ asset('images/sms.svg') }}"> {{ $plant['email'] ?? '' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="sales-manager-info">
                            <h1>Our Team Members</h1>
                            <div class="row">
                                @foreach ($plant['sales_managers'] as $manager)
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
                                                    <img src="{{ asset('images/call.svg') }}"> {{ !empty($manager['phone']) ? '+1' . $manager['phone'] : 'N/A' }}
                                                </div>
                                                <div class="sales-manager-contact">
                                                    <img src="{{ asset('images/sms.svg') }}"> -  {{ $manager['email']  ?? 'N/A'}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if (empty($plant['sales_managers']))
                                    <p>No sales managers available</p>
                                @endif
                            </div>
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
                        <div class="ss-modal-delete-icon"><img src="{{ asset('images/delete.svg') }}"></div>
                        <h2>Delete listed plant</h2>
                        <p id="delete-message">Are you sure you want to delete this plant from the listing?</p>
                        <form id="delete-form" action="{{ url('delete-plant') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" id="plant-id" name="plant_id" value="{{ $plant['id'] }}">
                            <input type="hidden" id="plant-name" name="plant_name" value="{{ $plant['plant_name'] }}">
                            <div class="ss-modal-delete-action">
                                <button type="submit" class="yes-btn">Yes, Delete</button>
                                <button type="button" class="cancel-btn" data-bs-dismiss="modal"
                                    aria-label="Close">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
    <script>
        $(document).ready(function() {
            $('.delete-btn').click(function() {
                var plantId = $(this).data('plant-id');
                var plantName = $(this).data('plant-name');
                $('#plant-id').val(plantId);
                $('#plant-name').val(plantName);
                $('#delete-message').text('Are you sure you want to delete "' + plantName +
                    '" from the listing?');
            });

            $('#delete-form').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var baseUrl = "{{ url('/') }}";
                var manageLocationsUrl = baseUrl + '/manage-locations';
                $.ajax({
                    type: 'DELETE',
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        $('#deleteplants').modal('hide');
                        window.location.href =
                            'https://www.niletechinnovations.com/projects/seco/public/manufacturer/manage-locations'; // Reload the page after deletion
                    },
                    error: function(error) {
                        console.error('Error deleting plant:', error);
                    }
                });
            });
        });
    </script>

@endsection
