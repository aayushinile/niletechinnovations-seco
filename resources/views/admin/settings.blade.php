@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/home.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/header-footer.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/managelocations.css') }}">
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/OwlCarousel/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/fancybox/fancybox.css') }}">
    <script src="{{ asset('plugins/OwlCarousel/owl.carousel.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/fancybox/fancybox.umd.js') }}" type="text/javascript"></script>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-heading-section">
            <h2>Manage Settings</h2>
        </div>
        <form id="add-plant-form" action="{{ route('admin.save_setting') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="add-plants-section">
                <div class="add-plants-item">

                    <div class="add-plants-form">
                    <div class="row">
                        <div class="col-md-6 d-none">
                            <div class="form-group">
                                <h5>Shipping Cost of Single Wide (in $)</h5>
                                <input type="text" class="form-control" name="single_wide_cost"
                                    value="{{ isset($mergedData['sw']) ? $mergedData['sw']->shipping_cost : '' }}" 
                                    placeholder="$0">
                            </div>
                        </div>

                        <div class="col-md-6 d-none">
                            <div class="form-group">
                                <h5>Shipping Cost of Double Wide (in $)</h5>
                                <input type="text" class="form-control" name="double_wide_cost"
                                    value="{{ isset($mergedData['dw']) ? $mergedData['dw']->shipping_cost : '' }}" 
                                    placeholder="$0">
                                <span id="emailError" class="error-message"></span>
                            </div>
                        </div>

                        <div class="col-md-6 d-none">
                            <div class="form-group">
                                <h5>Shipping Cost of Single Wide & Double Wide (in $)</h5>
                                <input type="text" class="form-control" name="single_double_wide_cost"
                                    value="{{ isset($mergedData['sw_dw']) ? $mergedData['sw_dw']->shipping_cost : '' }}" 
                                    placeholder="$0" maxlength="10">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <h5>Set Miles for Plant search in App</h5>
                                <input type="number" class="form-control" name="set_miles" placeholder="0" value="{{ $mile['miles'] ?? 0}}">
                            </div>
                        </div>
                    </div>
                        <div class="add-plants-foot">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="savecreatebtn">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="loader-container" id="loader">
                <div class="loader"></div>
            </div>

        </form>
    </div>
@endsection
