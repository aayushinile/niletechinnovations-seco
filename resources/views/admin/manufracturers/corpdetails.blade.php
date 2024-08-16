@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/communityowners.css') }}">
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-card">
            <div class="card-header">
                <h2>Plant Detail</h2>
                <div class="search-filter wd2 d-none">
                    <div class="row g-1">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="btn-bl"data-bs-toggle="modal" data-bs-target="#MarkAsInactive">Mark As
                                    {{ $mfs->status == 1 ? 'Inactive' : 'Active' }}</a>
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
                                    <h2>{{ $mfs->business_name ?? 'N/A' }}</h2>
                                    <div
                                        class="status-text  {{ $mfs->status == 1 ? 'status-active' : 'status-inactive' }}">
                                        {{ $mfs->status == 1 ? 'Active' : 'Inactive' }}</div>
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
                                            <p>{{ $mfs->email ?? 'N/A' }}</p>
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
                                            <p>{{ $mfs->phone ? '+1 ' . $mfs->phone : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                


                <div class="added-comminity-section">
                <div class="row d-flex">
    <div class="col-md-12">
        <div class="added-comminity-card">
            <div class="added-comminity-head">
                <div class="col-md-10">
                    <h2>Added Plants ({{ count($plants) }})</h2>
                </div>
                @if ($plants->isNotEmpty())
                <div class="col-md-2 text-right">
                    <form action="{{ route('admin.plant.export') }}" method="GET">
                        <input type="hidden" value="{{$mfs->id}}" name="id">
                        <button type="submit" class="btnDownloadExcel">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            <div class="added-comminity-body">
                @foreach ($plants as $item)
                    <div class="added-comminity-item">
                        <div class="added-comminity-item-image">
                            <img src="{{ asset('images/defaultuser.png') }}">
                        </div>
                        <div class="added-comminity-item-text">
                            <h4>{{ $item->plant_name }}</h4>
                            <div class="added-comminity-location">
                                <img src="{{ asset('admin/images/location.svg') }}">
                                {{ $item->full_address }}
                            </div>
                            <a class="Viewbtn" href="{{ route('admin.manufracturers.show', encrypt($item->plant_id)) }}">View</a>
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
@endsection
