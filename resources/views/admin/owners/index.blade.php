@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/communityowners.css') }}">
    <style>
        a.btn-refresh {
            width: 36px;
            border: none;
            display: inline-block;
            height: 40px;
            text-align: center;
            background: var(--pink);
            color: #fff;
            line-height: 40px;
            border-radius: 5px;
        }
    </style>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-card">
            <div class="card-header">
                <h2>Community Owners</h2>
                <div class="search-filter wd5" style="width: 50%">
                    <div class="row g-1">
                        <div class="col-md-6 offset-md-5"">
                            <form action="" method="get">
                                <div class="form-group">
                                    <div class="search-form-group">
                                        <input type="text" name="search" class="form-control"
                                            @if (request()->has('search')) value="{{ request('search') }}" @endif
                                            placeholder="Search By Name, Location, Email & Phone…">
                                        <span class="search-icon"><img
                                                src="{{ asset('admin/images/search-icon.svg') }}"></span>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-1">
                            <a class="btn-refresh" href="{{ route('admin.community.owners') }}"> <i class="fa fa-refresh"
                                    aria-hidden="true"></i></a>

                        </div>
                        <div class="col-md-5 d-none">
                            <div class="form-group">
                                <div class="input-group calendar-input-group">
                                    <input type="text" class="form-control" name="date" id="date"
                                        value="{{ request()->has('date') ? date('m-d-Y', strtotime(request('date'))) : '' }}"
                                        placeholder="MM-DD-YYYY" readonly><span class="input-group-addon calendar-icon-info"
                                        id="datepicker-icon">
                                        <!-- SVG calendar image -->
                                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9 1V3M3 1V3M0.5 5H11.5M1 2H11C11.2761 2 11.5 2.22386 11.5 2.5V12.5C11.5 12.7761 11.2761 13 11 13H1C0.723858 13 0.5 12.7761 0.5 12.5V2.5C0.5 2.22386 0.723858 2 1 2Z"
                                                stroke="#4A4A4B" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="ss-card-table">
                    <div class="user-table-list">
                        @forelse ($owners as $item)
                            <div class="user-table-item">
                                <div class="row g-1 align-items-center">
                                    <div class="col-md-4">
                                        <div class="user-profile-item">
                                            <div class="user-profile-media"><img
                                                    src="{{ asset('images/defaultuser.png') }}"></div>
                                            <div class="user-profile-text">
                                                <h2>{{ $item->business_name }}</h2>
                                                <div
                                                    class="status-text   {{ $item->status == 1 ? 'status-active' : 'status-inactive' }}">
                                                    {{ $item->status == 1 ? 'Active' : 'Inactive' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row g-1 align-items-center">


                                            <div class="col-md-4">
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
                                                        <p>{{ $item->mobile }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-1 offset-md-3 text-end">
                                                <div class="action-item-text">
                                                    <a class="action-btn"
                                                        href="{{ route('admin.community.owners.show', encrypt($item->id)) }}"><img
                                                            src="{{ asset('admin/images/arrow-right.svg') }}"></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div>
                                <p class="text-center">No results found</p>
                            </div>
                        @endforelse



                    </div>

                    @if (method_exists($owners, 'hasPages'))
                        <div class="ss-table-pagination">
                            <ul class="ss-pagination">
                                @if ($owners->onFirstPage())
                                    <li class="disabled" id="example_previous">
                                        <a href="#" aria-controls="example" data-dt-idx="0" tabindex="0"
                                            class="page-link">Previous</a>
                                    </li>
                                @else
                                    <li id="example_previous">
                                        <a href="{{ $owners->previousPageUrl() }}" aria-controls="example" data-dt-idx="0"
                                            tabindex="0" class="page-link">Previous</a>
                                    </li>
                                @endif

                                @foreach ($owners->getUrlRange(1, $owners->lastPage()) as $page => $url)
                                    <li class="{{ $owners->currentPage() == $page ? 'active' : '' }}">
                                        <a href="{{ $url }}" aria-controls="example"
                                            data-dt-idx="{{ $page }}" tabindex="0"
                                            class="page-link">{{ $page }}</a>
                                    </li>
                                @endforeach

                                @if ($owners->hasMorePages())
                                    <li class="next" id="example_next">
                                        <a href="{{ $owners->nextPageUrl() }}" aria-controls="example" data-dt-idx="7"
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
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('date').addEventListener('change', function() {
            var selectedValue = this.value;
            var currentUrl = new URL(window.location.href);

            // Add or update the 'run_id' parameter
            currentUrl.searchParams.set('date', selectedValue);

            // Reload the page with the new URL
            window.location.href = currentUrl.toString();
        });
    </script>
@endsection
