@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/home.css') }}">
    <style>
        .loc {
            min-height: 100px
        }
    </style>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="overview-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="overview-enquiries-card bl-bg-card">
                                <div class="overview-enquiries-text">
                                    <a href="{{ route('admin.community.owners') }}">
                                    <h1>Total Registered CO/Retailer</h1>
                                    <p>{{ $owners }}</p>
                                    </a>
                                </div>
                                <div class="overview-enquiries-img"><img src="images/Community-Owners1.svg" height="64"
                                        alt=""></div>
                            </div>
                        </div>

                        <div class="col-md-1 d-none">
                            <div class="overview-enquiries-card gr-bg-card">
                                <div class="overview-enquiries-text">
                                <a href="{{ route('admin.manufracturers') }}">
                                    <h1>Total Registered Plant Rep.</h1>
                                    <p>{{ $total_manufacturer_plant }}</p>
                                </a>
                                </div>
                                <div class="overview-enquiries-img"><img src="images/manufacturers1.svg" height="64"
                                        alt=""></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="overview-enquiries-card bl-bg-card">
                                <div class="overview-enquiries-text">
                                <a href="{{ route('admin.enquiries') }}">
                                    <h1>Total Enquiries</h1>
                                    <p>{{ $enquiries }}</p>
                                </a>
                                </div>
                                <div class="overview-enquiries-img"><img src="{{asset('images/enquiries-icon.svg')}}" height="64"
                                        alt=""></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="overview-enquiries-card gr-bg-card">
                                <div class="overview-enquiries-text">
                                <a href="{{ route('admin.manufracturers.corporate') }}">
                                    <h1>Total Registered Corporate Plant Owners</h1>
                                    <p>{{ $total_manufacturer_corp }}</p>
                                    </a>
                                </div>
                                <div class="overview-enquiries-img"><img src="{{asset('images/building-4.svg')}}" height="64"
                                        alt=""></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 d-none">
                    <div class="overview-card">
                        <div class="overview-card-head">
                            <div class="overview-location-item">
                                <div class="overview-location-content">
                                    <div class="overview-location-content-text">
                                        <p>Total Manufacturer listed Plants</p>
                                        <h2>{{ $plant_with_manufacturer }}</h2>
                                    </div>
                                    <div class="overview-location-content-icon">
                                        <img src="images/location-icon.svg">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="overview-card-body">
                            <div class="row g-1">
                                <div class="col-md-9">
                                    <div class="row g-1">
                                        @foreach ($plant_groups as $item)
                                            <div class="col-md-4">
                                                <div class="overview-location-item">
                                                    <div class="overview-location-content">
                                                        <div class="overview-location-content-text">
                                                            <p style="font-weight: bold">
                                                                {{ substr($item->plant_name, 0, 17) }}{{ strlen($item->plant_name) > 17 ? '...' : '' }}
                                                            </p>
                                                            <h2 style="font-size: 13px;font-weight:normal">
                                                                {{ substr($item->full_address, 0, 35) }}{{ strlen($item->full_address) > 35 ? '...' : '' }}
                                                            </h2>
                                                        </div>
                                                        <div class="overview-location-content-icon">
                                                            <img src="images/location-icon.svg">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {{-- <div class="overview-location-item">
                                        <div class="overview-location-content" style="cursor: pointer"
                                            onclick="location.replace('{{ route('admin.manufracturers') }}')">
                                            <div class="overview-location-content-text">
                                                <h2>View More</h2>
                                            </div>
                                            <div class="overview-location-content-icon">
                                                
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="overview-viewmore">
                                        <div class="overview-viewmore-text">View More</div>
                                        <div class="overview-viewmore-action"><a
                                                href="{{ route('admin.manufracturers') }}"><img
                                                    src="images/arrow-right.svg"></a></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="ss-card">
            <div class="card-header">
                <h2>Recent Registered Plant Owners</h2>
                <div class="search-filter wd6">

                </div>
            </div>
            <div class="card-body">
    <div class="ss-card-table">
    <div class="" style="overflow-x: sc !important; width: 100% !important;">
        <table class="table">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Location</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($manufacturer_request as $item)
                    @php
                        $plant = \App\Models\ManufacturerAttributes::where('manufacturer_id', $item->id)->first();
                        $image = $plant ? \App\Models\PlantMedia::where('plant_id', $plant->id)->first() : null;
                    @endphp
                    <tr>
                        <td>
                            <div class="user-profile-item">
                                <div class="user-profile-media">
                                    @if ($item->image)
                                        <img src="{{ asset('upload/manufacturer-image/' . $item->image) }}" style="width: 50px; height: 50px;">
                                    @else
                                        <img src="{{ asset('images/default-user-2.png') }}" style="width: 50px; height: 50px;">
                                    @endif
                                </div>
                                <div class="user-profile-text">
                                    <h2>
                                    @if(!empty($item->business_name))
                                        {{ $item->business_name }}
                                    @else
                                        N/A
                                    @endif
                                    </h2>
                                    <div class="status-text status-active">
                                        {{ $item->status ? 'Active' : 'Inactive' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $item->full_address ?? 'N/A' }}</td>
                        <td>{{ $item->email ?? 'N/A' }}</td>
                        <td style="width:12%">{{ $item->phone ? '+1 ' . $item->phone : 'N/A' }}</td>
                        <td>
                            <div class="action-item-text">
                                @if($item->plant_type == 'plant_rep')
                                    @php
                                        $plant_id = \App\Models\Plant::where('manufacturer_id', $item->id)->first();
                                    @endphp
                                    @if(!empty($plant_id))
                                        <a class="action-btn" href="{{ route('admin.manufracturers.show', encrypt($plant_id->id)) }}">
                                            <img src="{{ asset('images/arrow-right.svg') }}">
                                        </a>
                                    @else
                                        <a class="action-btn" href="{{ route('admin.manufracturers') }}">
                                            <img src="{{ asset('images/arrow-right.svg') }}">
                                        </a>
                                    @endif
                                @else 
                                    <a class="action-btn" href="{{ route('admin.manufracturers.corporateshow', encrypt($item->id)) }}">
                                        <img src="{{ asset('images/arrow-right.svg') }}">
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
</div>
        </div>
    </div> <!-- Approverequest -->
    <div class="modal ss-modal fade" id="Approverequest" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-form">
                        <div class="MarkAsInactive-content">
                            <div class="MarkAsInactive-image">
                                <img src="{{ asset('admin/images/MarkAsInactive.svg') }}">
                            </div>
                            <h2>Are you sure want to Accept “<span id="n"></span>” Registration Request?</h2>
                            <div class="note-text">Note: manufacturer will not be able to login again</div>

                            <form id="approve_form" method="post">
                                @csrf
                                <div class="MarkAsInactive-action">
                                    <button class="btn-Approve">Approve Request</button>
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
