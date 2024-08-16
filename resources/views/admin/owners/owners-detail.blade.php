@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/communityowners.css') }}">
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-card">
            <div class="card-header">
                <h2>Community Owners Detail</h2>
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
                                <div class="user-profile-media"><img src="{{ asset('images/defaultuser.png') }}">
                                </div>
                                <div class="user-profile-text">
                                    <h2>{{ $owner->business_name }}</h2>
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
                        <div class="col-md-8">
                            <div class="added-comminity-card">
                                <div class="added-comminity-head">
                                    <h2>Added Community ({{ count($community) }})</h2>
                                </div>
                                <div class="added-comminity-body">
                                    @foreach ($community as $item)
                                        <div class="added-comminity-item">
                                            <div class="added-comminity-item-image">
                                            <img src="{{ $item->value ? asset('upload/community_image/' . $item->value) : asset('images/defaultuser.png') }}">
                                            </div>
                                            <div class="added-comminity-item-text">
                                                <h4>{{ $item->community_name }}</h4>
                                                <div class="added-comminity-location"><img
                                                        src="{{ asset('admin/images/location.svg') }}">
                                                    {{ $item->community_address }}</div>
                                                <p>{{ $item->description }}.</p>
                                                <a class="Viewbtn"
                                                    href="{{ route('admin.community.show', encrypt($item->community_id)) }}">View</a>
                                            </div>
                                        </div>
                                    @endforeach



                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="contacted-manufacturer-card">
                            <div class="contacted-manufacturer-head row align-items-center">
            <div class="col-md-8">
                <h2>Contacted Manufacturer ({{ count($contact_m) }})</h2>
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
