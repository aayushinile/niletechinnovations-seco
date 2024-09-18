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
.loader-container {
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Semi-transparent black overlay */
        display: none;
        /* Initially hidden */
        justify-content: center;
        align-items: center;
    }

    .loader {
        border: 8px solid #f3f3f3;
        /* Light grey */
        border-top: 8px solid #3498db;
        /* Blue */
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
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-card">
            <div class="card-header">
                <h2>Community Owners / Retailers</h2>
                <div class="search-filter wd50">
                    <div class="row g-2">
                        <div class="col-md-4">
                             <form action="" method="get">
                                <div class="form-group">
                                    <div class="search-form-group">
                                        <input type="text" name="search" class="form-control"
                                            @if (request()->has('search')) value="{{ request('search') }}" @endif
                                            placeholder="Search">
                                        <span class="search-icon">
                                            <img src="{{ asset('admin/images/search-icon.svg') }}">
                                        </span>
                                    </div>
                                </div>
                                <!-- Export Button -->
                            </form>
                        </div>
                        <div class="col-md-3">
                        <select class="form-control select2" name="type" id="type" onchange="changeManu(this.value)">
                            <option value="">Select Type</option>
                            <option value="all" @if (request('type') == 'all') selected @endif>All</option>
                            <option value="2" @if (request('type') == '2') selected @endif>Community Owners</option>
                            <option value="1" @if (request('type') == '1') selected @endif>Retailers</option>
                        </select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <a class="btn-bl" style="background: var(--red); cursor:pointer !important; font-size: 14px;" id="deleteButton">Delete</a>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <a class="btn-refresh" href="{{ route('admin.community.owners') }}" > <i class="fa fa-refresh"
                                    aria-hidden="true"></i></a>

                        </div>

                        <div class="col-md-2">
                            <a href="{{ route('admin.community.owners', array_merge(request()->all(), ['download' => 1])) }}" class="btnDownloadExcel">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            </a>

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
                    <?php $s_no = 1; ?>
                        @forelse ($owners as $item)
                            <div class="user-table-item">
                                <div class="row g-1 align-items-center">
                                    <div class="col-md-3">
                                    <div class="usercheckbox-info">
                                    <div class="sscheckbox">
                                        <input type="checkbox" name="select_plant[]" value="{{ $item->id }}" id="{{ $item->id }}">
                                        <label for="{{ $item->id }}">&nbsp;</label>
                                            </div>
                                        <div class="user-profile-item">
                                            <div class="user-profile-media">
                                                @if(empty($item->image))
                                                <img
                                                    src="{{ asset('images/default-user-2.png') }}">
                                                @else 
                                                <img src="{{ asset('upload/profile-image/' . $item->image) }}">
                                                @endif
                                                </div>
                                            <div class="user-profile-text">
                                                <h2>{{ $item->fullname }}</h2>
                                                <div
                                                    class="status-text   {{ $item->status == 1 ? 'status-active' : 'status-inactive' }}">
                                                    {{ $item->status == 1 ? 'Approved' : 'Pending for Approval' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row g-1 align-items-center">
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
                                                        <p>@if ($item->mobile)
                    {{ substr($item->mobile, 0, 2) === '+1' ? $item->mobile : '+1 ' . $item->mobile }}
                @else
                    N/A
                @endif</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-content">
                                                        <h2>Type</h2>
                                                        <p> @if($item->type == 1)
                                                                Retailer
                                                            @else
                                                                Community Owner
                                                            @endif</p>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-3">
                                                <div class="user-contact-info">
                                                    <div class="user-contact-info-content">
                                                        <h2>Status</h2>
                                                        <div class="switch-toggle">
                                    <label class="toggle" for="myToggleClass_{{ $s_no }}">
                                        <input class="toggle__input myToggleClass" name="status" data-id="{{ $item->id }}" type="checkbox" id="myToggleClass_{{ $s_no }}" {{ $item->status == 1 ? 'checked' : '' }}>
                                        <div class="toggle__fill"></div>
                                    </label>
                                </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-1 text-end">
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
                             <?php $s_no++; ?>
                        @empty
                            <div>
                                <p class="text-center">No results found</p>
                            </div>
                           
                        @endforelse



                    </div>
                    @if ($owners->isNotEmpty())
                    @if (method_exists($owners, 'hasPages'))
                        <div class="ss-table-pagination">
                            <ul class="ss-pagination">
                                @if ($owners->onFirstPage())
                                    <li class="disabled" id="example_previous">
                                        <a href="#" aria-controls="example" data-dt-idx="0" tabindex="0"
                                            class="page-link">Prev</a>
                                    </li>
                                @else
                                    <li id="example_previous">
                                        <a href="{{ $owners->previousPageUrl() }}" aria-controls="example" data-dt-idx="0"
                                            tabindex="0" class="page-link">Prev</a>
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
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal ss-modal fade" id="MarkAsDeleteuser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ss-modal-form">
                    <div class="MarkAsInactive-content">
                        <div class="MarkAsInactive-image">
                            <img src="{{ asset('images/MarkAsInactive.svg') }}" alt="Delete Icon">
                        </div>
                        <h2>Are you sure you want to delete this account?</h2>
                        <form id="deleteForm" method="post">
                            @csrf
                            <input type="hidden" name="user_id" id="user-ids">
                            <div class="MarkAsInactive-action">
                                <button type="submit" class="btn-MarkAsInactive">Yes, Confirm</button>
                                <button class="cancel-btn" type="button" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
   
    <div class="loader-container" id="loader">
    <div class="loader"></div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#deleteButton').on('click', function(e) {
            e.preventDefault();

            // Get selected checkboxes
            var selectedIds = [];
            $('input[name="select_plant[]"]:checked').each(function() {
                selectedIds.push($(this).val());
            });
            console.log(selectedIds);
            // Check if at least one checkbox is selected
            if (selectedIds.length === 0) {
                // Show error alert if no checkbox is selected
                alert('Please select at least one User.');

                return false; // Prevent further execution and stop the modal from showing
            }

            // Only trigger the modal if a checkbox is selected
            document.getElementById('user-ids').value = selectedIds.join(',');
            $('#MarkAsDeleteuser').modal('show');
        });

        // Delete form submission
        $('#deleteForm').on('submit', function(e) {
            e.preventDefault();
            
            // Show loader (if needed)
            $('#loader').show();

            // AJAX request
            $.ajax({
                url: "{{ route('delete_user_account_multiple') }}",  // Ensure this route is correctly defined in your web.php
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Hide loader
                    $('#loader').hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'The account has been successfully deleted.',
                        timer: 2000, // Show the success message for 2 seconds
                        timerProgressBar: true,
                        willClose: () => {
                            // Redirect to the index page after the SweetAlert closes
                            window.location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    // Hide loader
                    $('#loader').hide();
                    
                    // Show error message using SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: xhr.responseJSON.message || 'An error occurred while deleting the account.',
                    });
                }
            });
        });
    });
</script>
    <script>
$(document).ready(function() {
    // Attach the event handler dynamically to all toggle inputs
    $('.toggle__input').on('change', function() {
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
            url: baseUrl + '/toggleUserRequestStatus',
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
            }
        });
    });
});
</script>

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


<script>
        $(document).ready(function() {
            $("select").select2();

        });
        function GetData(message) {
            document.getElementById("message").innerText =
                message;
        }


        function changeManu(val) {
            var selectedValue = val;
            var currentUrl = new URL(window.location.href);

            // Add or update the 'run_id' parameter
            currentUrl.searchParams.set('type', selectedValue);
            if (val == 0) {
                currentUrl.searchParams.delete('type');

            }
            // Reload the page with the new URL
            window.location.href = currentUrl.toString();
        }
    </script>
    
@endsection
