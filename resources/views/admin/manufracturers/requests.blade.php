@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/home.css') }}">
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-card">
            <div class="card-header">
                <h2>Manufacturer Registration Requests ({{ count($manufracturers) }} New Request)</h2>
                <div class="search-filter wd6">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="search-form-group">
                                <input type="text" name="search" class="form-control"
                                    @if (request()->has('search')) value="{{ request('search') }}" @endif
                                    placeholder="Search By Name, Email & Phone…">
                                <span class="search-icon"><img src="{{ asset('admin/images/search-icon.svg') }}"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="ss-card-table">
                    <div class="user-table-list">
                        @foreach ($manufracturers as $item)
                            <div class="user-table-item">
                                <div class="row g-1 align-items-center">
                                    <div class="col-md-3">
                                        <div class="user-profile-item">
                                            <div class="user-profile-media"><img
                                                    src="{{ asset('admin/images/yureez mh-search-logo.png') }}"></div>
                                            <div class="user-profile-text">
                                                <h2>{{ $item->full_name }}</h2>
                                                <div
                                                    class="status-text {{ $item->status == 1 ? 'status-active' : 'status-inactive' }}">
                                                    {{ $item->status == 1 ? 'Active' : 'Inactive' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row g-1 align-items-center">
                                            <div class="col-md-3">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-icon">
                                                        <img src="{{ asset('admin/images/location.svg') }}">
                                                    </div>
                                                    <div class="user-contact-info-content">
                                                        <h2>Location</h2>
                                                        <p>{{ $item->manufacturer_address }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-icon">
                                                        <img src="{{ asset('admin/images/sms.svg') }}">
                                                    </div>
                                                    <div class="user-contact-info-content">
                                                        <h2>Email</h2>
                                                        <p>{{ $item->email }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-icon">
                                                        <img src="{{ asset('admin/images/call.svg') }}">
                                                    </div>
                                                    <div class="user-contact-info-content">
                                                        <h2>Phone</h2>
                                                        <p>+1{{ $item->mobile }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 text-end">
                                                <div class="action-item-text">
                                                    <a class="Approve-btn" data-bs-toggle="modal"
                                                        onclick="$('#approve_form').attr('action','{{ route('admin.manufracturers.requests.approve', $item->id) }}')"
                                                        data-bs-target="#Approverequest">Approve</a>
                                                    <a class="Reject-btn" href="#">Reject</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach


                    </div>


                    <div class="ss-table-pagination">
                        <ul class="ss-pagination">
                            @if ($manufracturers->onFirstPage())
                                <li class="disabled" id="example_previous">
                                    <a href="#" aria-controls="example" data-dt-idx="0" tabindex="0"
                                        class="page-link">Previous</a>
                                </li>
                            @else
                                <li id="example_previous">
                                    <a href="{{ $manufracturers->previousPageUrl() }}" aria-controls="example"
                                        data-dt-idx="0" tabindex="0" class="page-link">Previous</a>
                                </li>
                            @endif

                            @foreach ($manufracturers->getUrlRange(1, $manufracturers->lastPage()) as $page => $url)
                                <li class="{{ $manufracturers->currentPage() == $page ? 'active' : '' }}">
                                    <a href="{{ $url }}" aria-controls="example"
                                        data-dt-idx="{{ $page }}" tabindex="0"
                                        class="page-link">{{ $page }}</a>
                                </li>
                            @endforeach

                            @if ($manufracturers->hasMorePages())
                                <li class="next" id="example_next">
                                    <a href="{{ $manufracturers->nextPageUrl() }}" aria-controls="example" data-dt-idx="7"
                                        tabindex="0" class="page-link">Next</a>
                                </li>
                            @else
                                <li class="disabled" id="example_next">
                                    <a href="#" aria-controls="example" data-dt-idx="7" tabindex="0"
                                        class="page-link">Next</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approverequest -->
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
                            <h2>Are you sure want to Accept “KIT Custom Homes” Registration Request?</h2>
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
