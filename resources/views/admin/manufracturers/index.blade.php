@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/manufacturers.css') }}">
    <style>
        /*input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            outline: none;
            width: 16px;
            height: 16px;
            border: 1px solid #ccc;
            border-radius: 3px;
            background-color: white;
            cursor: pointer;
            position: relative;
        }*/

        /* Style the checkbox when checked */
       /* input[type="checkbox"]:checked {
            background-color: var(--pink);
            border-color: var(--pink);
        }*/

        /* Optional: Add a checkmark */
        /*input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 5px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }*/

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

        .switch-toggle {
            display: inline-block;
            position: relative;
        }

        .toggle {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle__input {
            display: none;
        }

        .toggle__fill {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .toggle__fill:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        .toggle__input:checked + .toggle__fill {
            background-color: var(--pink);
        }

        .toggle__input:checked + .toggle__fill:before {
            transform: translateX(26px);
        }
    </style>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-card">
            <div class="card-header">
                <h2>Plants({{ $total }})</h2>
                <div class="search-filter wd80">
                    <div class="row g-1" style="justify-content: end;">

                        <div class="col-md-3  d-none">
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


                        <div class="col-md-9">
                            <div class="search-form-refresh-group">
                                <div class="search-form-input-group">
                                    <form action="" method="get">
                                        <div class="form-group">
                                            <div class="search-form-group">
                                                <input type="text" name="search" class="form-control"
                                                    @if (request()->has('search')) value="{{ request('search') }}" @endif
                                                    placeholder="Search">
                                                <span class="search-icon"><img src="{{ asset('images/search-icon.svg') }}"></span>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                     <div class="form-group">
                                        <select name="status" class="form-control"  onchange="changeStatus(this.value)">
                                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>SHOW ALL</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select> 
                                    </div> 
                                </div>
                                <div class="search-form-refresh-action">
                                    <a class="btn-refresh" href="{{ route('admin.manufracturers') }}"> <i class="fa fa-refresh"
                                    aria-hidden="true"></i></a>

                                    <a href="{{ route('admin.manufracturers', array_merge(request()->all(), ['download' => 1])) }}" class="btnDownloadExcel">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    </a>    
                                </div>
                            </div> 
                        </div>
                      
                        <!-- <div class="col-md-2 d-none">
                            <div class="form-group">
                                <a class="btn-bl"  style="background-color: var(--green);"
                                    data-bs-toggle="modal" id="open-activate-modal">Mark
                                    As Active</a>
                            </div>
                        </div> -->
                        <!-- <div class="col-md-2 d-none">
                            <div class="form-group">
                                <a class="btn-bl" href="" style="background-color: var(--red);"
                                    data-bs-toggle="modal" id="open-inactivate-modal">Mark
                                    As Inactive</a>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="ss-card-table">
                    <div class="user-table-list">
                    <?php $s_no = 1; ?>
                        @forelse ($manufracturers as $item)
                        @php 
                        $manufacturer = \App\Models\PlantLogin::where('id',$item->manufacturer_id)->first();
                        @endphp
                        @if ($manufacturer)
                            <div class="user-table-item">
                                <div class="row g-1 align-items-center">
                                    <div class="col-md-3">
                                        <div class="usercheckbox-info">
                                            <div class="sscheckbox">
                                                <input type="checkbox" name="select_plant[]" value="{{ $manufacturer->id }}" id="{{ $manufacturer->id }}">
                                                <label for="{{ $manufacturer->id }}">&nbsp;</label>
                                            </div>
                                            @php
                                                $plant = \App\Models\Plant::where('manufacturer_id', $item->id)->first();
                                                if ($plant) {
                                                    $image = \App\Models\PlantMedia::where('plant_id', $plant->id)->first();
                                                } else {
                                                    $image = null; 
                                                }
                                                
                                            @endphp
                                            <div class="user-profile-item">
                                                <div class="user-profile-media">
                                                    @if ($manufacturer->image)
                                                        <img
                                                            src="{{ asset('upload/manufacturer-image/' . $manufacturer['image']) }}">
                                                    @elseif($image)
                                                    <img
                                                    src="{{ asset('upload/manufacturer-image/' . $image['image_url']) }}">
                                                    @else
                                                        <img src="{{ asset('images/defaultuser.png') }}">
                                                    @endif
                                                </div>
                                                <div class="user-profile-text">
                                                <h2>{{ $manufacturer->plant_name ?? $manufacturer->business_name ?? 'N/A' }}</h2>
                                                    <div
                                                        class="status-text {{ $manufacturer->status == 1 ? 'status-active' : 'status-inactive' }}">
                                                        {{ $manufacturer->status == 1 ? 'Active' : 'Inactive' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row g-1 align-items-center">
                                            <div class="col-md-3">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-icon">
                                                        <img src="{{ asset('images/location.svg') }}">
                                                    </div>
                                                    <div class="user-contact-info-content">
                                                        <h2>Mgf. Location</h2>
                                                        <p>{{ $manufacturer->full_address ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-icon">
                                                        <img src="{{ asset('images/sms.svg') }}">
                                                    </div>
                                                    <div class="user-contact-info-content">
                                                        <h2>Email</h2>
                                                        <p>{{ $manufacturer->email ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-icon">
                                                        <img src="{{ asset('images/call.svg') }}">
                                                    </div>
                                                    <div class="user-contact-info-content">
                                                        <h2>Phone</h2>
                                                        <p>{{ $manufacturer->phone ? '+1 ' . $manufacturer->phone : 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @php 
                                                $type = 'N/A';
                                                if($manufacturer->plant_type === 'corp_rep'){
                                                    $type =  'Corp. Representative';
                                                }elseif($manufacturer->plant_type === 'plant_rep'){
                                                    $type = ' Plant Representative';
                                                }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-content">
                                                        <h2>Status</h2>
                                                        <div class="switch-toggle">
                                                            <label class="toggle" for="myToggleClass_{{ $s_no }}">
                                                                <input class="toggle__input myToggleClass" name="status" data-id="{{ $item->id }}" type="checkbox" id="myToggleClass_{{ $s_no }}" {{ $manufacturer->status == 1 ? 'checked' : '' }}>
                                                                <div class="toggle__fill"></div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-1 text-end">
                                                <div class="action-item-text">
                                                    <a class="action-btn"
                                                        href="{{ route('admin.manufracturers.show', encrypt($item->id)) }}">
                                                        <img src="{{ asset('images/arrow-right.svg') }}">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $s_no++; ?>
                            @endif
                            
                        @empty
                            <p class="text-center">No records found</p>
                        @endforelse
                    </div>

                    @if (method_exists($manufracturers, 'hasPages'))
                        <div class="ss-table-pagination">
                            <ul class="ss-pagination">
                                @if ($manufracturers->onFirstPage())
                                    <li class="disabled" id="example_previous">
                                        <a href="#" aria-controls="example" data-dt-idx="0" tabindex="0"
                                            class="page-link">Prev</a>
                                    </li>
                                @else
                                    <li id="example_previous">
                                        <a href="{{ $manufracturers->previousPageUrl() }}" aria-controls="example"
                                            data-dt-idx="0" tabindex="0" class="page-link">Prev</a>
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
                                        <a href="{{ $manufracturers->nextPageUrl() }}" aria-controls="example"
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
    </div>
    <div class="modal ss-modal fade" id="inactivePlant" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-delete">
                        {{-- <div class="ss-modal-delete-icon"><img src=""></div> --}}
                        <p id="delete-message">Are you sure you want to In-activate these Plants?</p>
                        <form id="inactivate-form" method="POST">
                            @csrf
                            <input type="hidden" id="plant-ids" name="plant_ids">
                            <input type="hidden" id="status" name="status" value="0">
                            <div class="ss-modal-delete-action">
                                <button type="button" id="confirm-inactivate" class="yes-btn">Yes, Confirm</button>
                                <button type="button" class="cancel-btn" data-bs-dismiss="modal"
                                    aria-label="Close">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal ss-modal fade" id="activePlant" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="ss-modal-delete">
                        {{-- <div class="ss-modal-delete-icon"><img src=""></div> --}}
                        <p id="delete-message">Are you sure you want to Activate these Plants?</p>
                        <form id="activate-form" method="POST">
                            @csrf
                            <input type="hidden" id="plant-idss" name="plant_ids">
                            <input type="hidden" id="status" name="status" value="1">
                            <div class="ss-modal-delete-action">
                                <button type="button" id="confirm-activate" class="yes-btn">Yes, Confirm</button>
                                <button type="button" class="cancel-btn" data-bs-dismiss="modal"
                                    aria-label="Close">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
$(document).ready(function() {
    // Attach the event handler using event delegation
    $(document).on('change', '.toggle__input', function() {
        // Get the new status and the data-id of the clicked toggle
        var newStatus = this.checked ? '1' : '0';
        var request_id = $(this).attr("data-id");
        var baseUrl = "{{ url('/') }}";

        console.log('Sending AJAX request with:', {
            request_id: request_id,
            status: newStatus,
            _token: '{{ csrf_token() }}'
        });

        // Perform the AJAX request to toggle the status
        $.ajax({
            url: baseUrl + '/set_status',
            type: 'POST',
            data: {
                request_id: request_id,
                status: newStatus,
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Response received:', response);
                if (response.success) {
                    toastr.success(response.message);
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(error) {
                console.error('Error toggling user status:', error);
                toastr.error('There was an error processing your request.');
            }
        });
    });
});
</script>
    <script>
        document.getElementById('open-inactivate-modal').addEventListener('click', function(e) {
            e.preventDefault();
            let selectedIds = [];
            document.querySelectorAll('input[name="select_plant[]"]:checked').forEach(function(checkbox) {
                selectedIds.push(checkbox.value);
            });

            if (selectedIds.length > 0) {
                document.getElementById('plant-ids').value = selectedIds.join(',');
                $("#inactivePlant").modal("show")

            } else {
                alert('Please select at least one plant.');
                e.stopPropagation();
            }
        });



        document.getElementById('open-activate-modal').addEventListener('click', function(e) {
            e.preventDefault();
            let selectedIds = [];
            document.querySelectorAll('input[name="select_plant[]"]:checked').forEach(function(checkbox) {
                selectedIds.push(checkbox.value);
            });
            console.log(selectedIds);

            if (selectedIds.length > 0) {
                document.getElementById('plant-idss').value = selectedIds.join(',');
                $("#activePlant").modal("show")
            } else {
                alert('Please select at least one plant.');
                e.stopPropagation();
            }
        });

        document.getElementById('confirm-inactivate').addEventListener('click', function(e) {
            e.preventDefault();
            let form = document.getElementById('inactivate-form');
            let formData = new FormData(form);
            fetch("{{ route('set_status') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('An error occurred while updating the status.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });



        document.getElementById('confirm-activate').addEventListener('click', function(e) {
            e.preventDefault();
            let form = document.getElementById('activate-form');
            let formData = new FormData(form);
            console.log(formData);
            fetch("{{ route('set_statuss') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('An error occurred while updating the status.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
    <script>
        document.getElementById('date').addEventListener('change', function() {
            var selectedValue = this.value;
            var currentUrl = new URL(window.location.href);

            // Add or update the 'run_id' parameter
            currentUrl.searchParams.set('date', selectedValue);
            if (selectedValue == "") {
                currentUrl.searchParams.delete('date');

            }
            // Reload the page with the new URL
            window.location.href = currentUrl.toString();
        });

        function changeStatus(val) {
            var selectedValue = val;
            var currentUrl = new URL(window.location.href);
            console.log(selectedValue);
            // If the selected value is empty, remove the 'status' parameter
            if (selectedValue === '') {
                currentUrl.searchParams.delete('status');
            } else {
                // Otherwise, set the 'status' parameter to the selected value
                currentUrl.searchParams.set('status', selectedValue);
            }

            // Reload the page with the new URL
            window.location.href = currentUrl.toString();
        }
    </script>
@endsection
