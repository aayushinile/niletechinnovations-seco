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
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="overview-enquiries-card bl-bg-card">
                                <div class="overview-enquiries-text">
                                    <h1>Total Registered Community Owners</h1>
                                    <p>{{ $owners }}</p>
                                </div>
                                <div class="overview-enquiries-img"><img src="images/Community-Owners1.svg" height="64"
                                        alt=""></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="overview-enquiries-card gr-bg-card">
                                <div class="overview-enquiries-text">
                                    <h1>Total Registered Plants</h1>
                                    <p>{{ $total_manufacturer }}</p>
                                </div>
                                <div class="overview-enquiries-img"><img src="images/manufacturers1.svg" height="64"
                                        alt=""></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
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
                <h2>Recent Registered Plants</h2>
                <div class="search-filter wd6">

                </div>
            </div>
            <div class="card-body">
                <div class="ss-card-table">
                    <div class="user-table-list">
                        @foreach ($manufacturer_request as $item)
                            @php
                                $plant = \App\Models\Plant::where('manufacturer_id', $item->id)->first();
                                if ($plant) {
                                    $image = \App\Models\PlantMedia::where('plant_id', $plant->id)->first();
                                } else {
                                    $image = null;
                                }
                            @endphp
                            <div class="user-table-item">
                                <div class="row g-1 align-items-center">
                                    <div class="col-md-3">
                                        <div class="user-profile-item">
                                            <div class="user-profile-media">
                                                @if ($image)
                                                    <img
                                                        src="{{ asset('upload/manufacturer-image/' . $image['image_url']) }}">
                                                @else
                                                    <img src="{{ asset('images/defaultuser.png') }}">
                                                @endif
                                            </div>
                                            <div class="user-profile-text">
                                                <h2>{{ $item->plant_name }}</h2>
                                                <div class="status-text status-active">
                                                    {{ $item->status ? 'Active' : 'Inactive' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row g-1 align-items-center">
                                            <div class="col-md-4">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-icon">
                                                        <img src="images/location.svg">
                                                    </div>
                                                    <div class="user-contact-info-content">
                                                        <h2>Location</h2>
                                                        <p>{{ $item->full_address ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-icon">
                                                        <img src="images/sms.svg">
                                                    </div>
                                                    <div class="user-contact-info-content">
                                                        <h2>Email</h2>
                                                        <p>{{ $item->email ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-icon">
                                                        <img src="images/call.svg">
                                                    </div>
                                                    <div class="user-contact-info-content">
                                                        <h2>Phone</h2>
                                                        <p>{{ $item->phone ? '+1 ' . $item->phone : 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- 
                                            <div class="col-md-3 text-end">
                                                <div class="action-item-text">
                                                    <a class="Approve-btn" data-bs-toggle="modal"
                                                        onclick="$('#n').text('{{ $item->manufacturer_name }}');$('#approve_form').attr('action','{{ route('admin.manufracturers.requests.approve', $item->id) }}')"
                                                        data-bs-target="#Approverequest">Approve</a>
                                                    <a class="Reject-btn" href="#">Reject</a>
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach



                    </div>
                    @if (method_exists($manufacturer_request, 'hasPages'))
                        <div class="ss-table-pagination">
                            <ul class="ss-pagination">
                                @if ($manufacturer_request->onFirstPage())
                                    <li class="disabled" id="example_previous">
                                        <a href="#" aria-controls="example" data-dt-idx="0" tabindex="0"
                                            class="page-link">Previous</a>
                                    </li>
                                @else
                                    <li id="example_previous">
                                        <a href="{{ $manufacturer_request->previousPageUrl() }}" aria-controls="example"
                                            data-dt-idx="0" tabindex="0" class="page-link">Previous</a>
                                    </li>
                                @endif

                                @foreach ($manufacturer_request->getUrlRange(1, $manufacturer_request->lastPage()) as $page => $url)
                                    <li class="{{ $manufacturer_request->currentPage() == $page ? 'active' : '' }}">
                                        <a href="{{ $url }}" aria-controls="example"
                                            data-dt-idx="{{ $page }}" tabindex="0"
                                            class="page-link">{{ $page }}</a>
                                    </li>
                                @endforeach

                                @if ($manufacturer_request->hasMorePages())
                                    <li class="next" id="example_next">
                                        <a href="{{ $manufacturer_request->nextPageUrl() }}" aria-controls="example"
                                            data-dt-idx="7" tabindex="0" class="page-link">Next</a>
                                    </li>
                                @else
                                    <li class="disabled" id="example_next">
                                        <a href="#" aria-controls="example" data-dt-idx="7" tabindex="0"
                                            class="page-link">Next</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif

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