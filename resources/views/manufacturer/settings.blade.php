
@extends('manufacturer.layouts')
<style>
    .add-plants-action a.addmoremanager {
        background: var(--pink);
        color: var(--white);
        padding: 10px 15px;
        border-radius: 5px;
        font-size: 14px;
        box-shadow: 0 4px 10px #5f0f5845;
        display: inline-block;
        position: relative;
    }
    .mapboxgl-ctrl-geocoder--input{
        background: #eee2ed !important;
        border-radius: 5px !important;
        font-size: 13px !important;
        border: 1px solid var(--border) !important;
        font-weight: 400 !important;
        height: auto !important;
        padding: 0.94rem 0.94rem !important;
        outline: 0 !important;
        width: 100%;
        display: inline-block !important;
        color: var(--pink) !important;
        box-shadow: 0px 8px 13px 0px rgba(0, 0, 0, 0.05) !important;
    }
    .mapboxgl-ctrl-geocoder--icon{
        display: none !important;
    }
    @media screen and (min-width: 640px) {
        .mapboxgl-ctrl-geocoder {
            width: auto !important; /* Overrides the width property */
            max-width: none !important; /* Overrides the max-width property */
        }
    }

    .loader-container {
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
            display: none; /* Initially hidden */
            justify-content: center;
            align-items: center;
        }

        .loader {
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            position: relative;
            top: 46%;
            left: 46%;

            
        }
        .loader-container.show {
            display: flex;
        }


        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
</style>
@section('content')
            <div class="body-main-content">
                <div class="ss-heading-section">
                    <h2>Manage Settings</h2>
                    <div class="search-filter wd20">
                        <div class="row g-1">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="addnewplant-btn">Back</button>
                                </div> 
                            </div>
                        </div>   
                    </div>
                </div>
                <form id="add-plant-form" action="{{ route('manufacturer.updateSettings') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="add-plants-section">
                    <div class="add-plants-item">
                        
                        <div class="add-plants-form">
                            <div class="row">

                                <div class="col-md-8">
                                    <div class="form-group">
                                        <h5 class="mb-4">Select Country</h5>
                                        <select class="form-control" name="country">
                                        @foreach($countryFlags as $country => $flag)
                                            <option value="{{ $country }}" {{ $user->country == $country ? 'selected' : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="add-plants-foot">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="Cancelbtn">Cancel</button>
                                    <button type="submit" class="savecreatebtn">Save & Create</button>
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


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
@endsection