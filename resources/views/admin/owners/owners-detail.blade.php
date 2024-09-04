@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/communityowners.css') }}">
    <style>
        .contacted-manufacturer-body.scrollable {
    max-height: 300px; /* Adjust this value as needed */
    overflow-y: auto;
    padding-right: 15px; /* Optional: to avoid cutting off content when scrolling */
}

.contacted-manufacturer-item {
    margin-bottom: 10px; /* Adjust spacing between items */
}
    </style>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-card">
            <div class="card-header">
                <h2>Community Owners/Retailer Detail</h2>
                <div class="search-filter wd2 d-none">
                    <div class="row g-1">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="btn-bl"data-bs-toggle="modal" data-bs-target="#MarkAsInactive">Mark As
                                    {{ $owner->status == 1 ? 'Inactive' : 'Active' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="user-table-item">
                    <div class="row g-1 align-items-center">
                        <div class="col-md-4">
                            <div class="user-profile-item">
                                <div class="user-profile-media">
                                @if(empty($owner->image))
                                <img
                                    src="{{ asset('images/defaultuser.png') }}">
                                @else 
                                <img src="{{ asset('upload/profile-image/' . $owner->image) }}">
                                @endif
                                </div>
                                <div class="user-profile-text">
                                    <h2>{{ $owner->fullname }}</h2>
                                    <div
                                        class="status-text  {{ $owner->status == 1 ? 'status-active' : 'status-inactive' }}">
                                        {{ $owner->status == 1 ? 'Active' : 'Inactive' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-1 align-items-center">


                                <div class="col-md-6">
                                    <div class="user-contact-info">
                                        <div class="user-contact-info-icon">
                                            <img src="{{ asset('admin/images/sms.svg') }}">
                                        </div>
                                        <div class="user-contact-info-content">
                                            <h2>Email</h2>
                                            <p>{{ $owner->email ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="user-contact-info">
                                        <div class="user-contact-info-icon">
                                            <img src="{{ asset('admin/images/call.svg') }}">
                                        </div>
                                        <div class="user-contact-info-content">
                                            <h2>Phone</h2>
                                            <p>{{ $owner->mobile ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="added-comminity-section">
                    <div class="row">
                    <div class="col-md-4">
                            <div class="contacted-manufacturer-card">
                            <div class="contacted-manufacturer-head row align-items-center">
                                <div class="col-md-8">
                                    <h2 style="color:#5f0f58">Saved Locations({{ count($saved_locations) }})</h2>
                                </div>
                                @if ($saved_locations->isNotEmpty())
                                <div class="contacted-manufacturer-body {{ count($saved_locations) > 3 ? 'scrollable' : '' }}">
                                    @foreach ($saved_locations as $item)
                                        <div class="contacted-manufacturer-item">
                                            <div class="contacted-manufacturer-item-image">
                                                <img src="{{ asset('images/location.svg') }}">
                                            </div>
                                            <div class="contacted-manufacturer-item-text">
                                                <h4>{{ $item->location }}</h4>
                                                <h4>{{$item->city}},{{$item->state}}</h4>
                                            </div>
                                        </div>
                                    @endforeach


                                </div>
                                @else 
                                <div class="contacted-manufacturer-body">
                                        <div class="contacted-manufacturer-item">
                                            <div class="contacted-manufacturer-item-text">
                                                <p class="no-data">No Data Found</p>
                                            </div>
                                        </div>


                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                            <div class="contacted-manufacturer-card">
                            <div class="contacted-manufacturer-head row align-items-center">
                            <div class="row align-items-center">
    <div class="col-md-2">
        <div class="user-profile-media">
            @if(empty($owner->company_image))
                <img src="{{ asset('images/defaultuser.png') }}" alt="Default User">
            @else 
                <img src="{{ asset('upload/profile-image/' . $owner->company_image) }}" alt="User Image">
            @endif
        </div>
    </div>
    <div class="col-md-10">
        <h2 style="color:#5f0f58">Business Info</h2>
    </div>
</div>
                                <div class="contacted-manufacturer-body mt-2">
                                        <div class="contacted-manufacturer-item">
                                            <div class="contacted-manufacturer-item-text">
                                            <h4>Type :  
                                                @if($owner->type == 1)
                                                    Retailer
                                                @else
                                                    Community Owner
                                                @endif
                                            </h4>
                                                
                                                <div class="contacted-manufacturer-item">
                                                    <div class="contacted-manufacturer-item-image">
                                                        <img src="https://showsearch.net/images/ic-user-square.svg">
                                                    </div>
                                                    <div>
                                                        <p>Business Name : {{$owner->business_name ?? 'N/A'}}</p>
                                                    </div>
                                                </div>

                                                <div class="contacted-manufacturer-item">
                                                    <div class="contacted-manufacturer-item-image">
                                                        <img src="https://showsearch.net/images/location.svg">
                                                    </div>
                                                    <div>
                                                        <p>{{$owner->business_address ?? 'N/A'}} </p>
                                                    </div>
                                                </div>

                                                <div class="contacted-manufacturer-item">
                                                    <div class="contacted-manufacturer-item-image">
                                                        <img src="https://showsearch.net/images/ic-receipt-edit.svg">
                                                    </div>
                                                    <div>
                                                        <p>MHs purchased per year : <span> {{$owner->no_of_mhs ?? 'N/A'}}</span> </p>
                                                    </div>
                                                </div>

                                                <div class="contacted-manufacturer-item">
                                                    <div class="contacted-manufacturer-item-image">
                                                        <img src="https://showsearch.net/images/ic-receipt-edit.svg">
                                                    </div>
                                                    <div>
                                                        <p> Communities/Retail Lots Owned : <span> {{$owner->no_of_communities ?? 'N/A'}} </span></p>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>


                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="col-md-4">
                            <div class="contacted-manufacturer-card">
                            <div class="contacted-manufacturer-head row align-items-center">
            <div class="col-md-8">
                <h2 style="color:#5f0f58">Contacted Manufacturer ({{ count($contact_m) }})</h2>
            </div>
            @if ($contact_m->isNotEmpty())
                                <div class="col-md-4 text-right">
                                    <form action="{{ route('admin.community.export') }}" method="GET">
                                        <input type="hidden" value="{{ $owner->id }}" name="id">
                                        <button type="submit" class="btnDownloadExcel">
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                                @endif
                                @if ($contact_m->isNotEmpty())
                                <div class="contacted-manufacturer-body">
                                    @foreach ($contact_m as $item)
                                        <div class="contacted-manufacturer-item">
                                            <div class="contacted-manufacturer-item-image">
                                                <a data-bs-toggle="modal" data-bs-target="#contactedmanufacturer">
                                                    <img src="{{ $item->image_url ? asset('upload/manufacturer-image/' . $item->image_url) : asset('images/defaultuser.png') }}">
                                                    </a>
                                            </div>
                                            <div class="contacted-manufacturer-item-text">
                                                <h4>{{ $item->plant_name }}</h4>
                                                <p>{{$item->full_address}}</p>
                                            </div>
                                        </div>
                                    @endforeach


                                </div>
                                @else 
                                <div class="contacted-manufacturer-body">
                                        <div class="contacted-manufacturer-item">
                                            <div class="contacted-manufacturer-item-text">
                                                <p class="no-data">No Data Found</p>
                                            </div>
                                        </div>


                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
    <!-- Mark As Inactive -->
    <div class="modal ss-modal fade" id="MarkAsInactive" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-form">
                        <div class="MarkAsInactive-content">
                            <div class="MarkAsInactive-image">
                                <img src="{{ asset('images/MarkAsInactive.svg') }}">
                            </div>
                            <h2>Are you sure want to Mark “{{ $owner->business_name }}” As
                                {{ $owner->status == 1 ? 'Inactive' : 'Active' }}</h2>
                            <div class="note-text">Note: manufacturer will not be able to login again</div>
                            <form action="{{ route('set_status') }}" method="post">
                                @csrf
                                <input type="hidden" name="status" value=" {{ $owner->status == 1 ? 0 : 1 }}">
                                <input type="hidden" name="table" value="users">
                                <input type="hidden" name="id" value="{{ $owner->id }}">
                                <div class="MarkAsInactive-action">

                                    <button class="btn-MarkAsInactive">Mark As
                                        {{ $owner->status == 1 ? 'Inactive' : 'Active' }}</button>
                                    <button class="cancel-btn" type="button" data-bs-dismiss="modal"
                                        aria-label="Close">Cancel</button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
