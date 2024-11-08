@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/communityowners.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/home.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/manufacturers.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') }}">
	<script src="{{asset('js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/managelocations.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .wd6{width: 60%;}
        .wd5{width: 50%;}
        .wd4{width: 40%;}
        .wd3{width: 30%;}
       .password-wrapper {
    position: relative;
}

        .password-wrapper .form-control {
            padding-right: 40px; /* Adjust padding to make space for the icon */
        }

        .password-wrapper .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px; /* Adjust position to fit your design */
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 2; /* Ensure the icon is above the input field */
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


    .status-rejected {
        color: var(--white);
        background: var(--red);
    }
    .status-pending {
        color: var(--white);
        background: var(--yellow);
    }
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
                <div class="search-filter "style="width:50%">
                    
                    <div class="row g-1">
                   
                    <div class="col-md-2">
                        <div class="form-group">
                            <a class="btn-bl"  style="background-color: var(--green);"
                                data-bs-toggle="modal" id="open-activate-modal">Approve</a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="btn-bl" href="" style="background-color: var(--red);"
                                data-bs-toggle="modal" id="open-inactivate-modal">Unapprove</a>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <a class="ChangePasswordbtn btn-bl" style="background-color: var(--green);" data-bs-toggle="modal" data-bs-target="#ChangePassword"  data-plant-id="{{ $mfs['id'] }}" data-email-id="{{ $mfs['email'] }}">Reset Credentials</a>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="addnewplant-btn" href="javascript:void(0);" onclick="document.getElementById('fileInput').click()" style="background-color: var(--green);font-weight:600;height:40px;">
                                <i class="fas fa-file-excel"></i> Import
                            </a>
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
                                <div class="user-profile-media"><img src="{{ asset('images/default-user-2.png') }}">
                                </div>
                                <div class="user-profile-text">
                                    <h2>{{ $mfs->business_name ?? 'N/A' }}</h2>
                                    <div
                                        class="status-text  {{ $mfs->status == 1 ? 'status-active' : 'status-inactive' }}">
                                        {{ $mfs->status == 1 ? 'Approved' : 'Pending' }}</div>
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


                

        
                <div class="added-comminity-head">
    <div class="d-flex justify-content-between align-items-center w-100">
        <h2 style="width: 12%;">Plants ({{ $count }})</h2>
        <div class="d-flex align-items-center w-100">
            <div class="search-filter" style="width: 35%;">
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
            </div>
            <div class="col-md-3">
                    <div class="form-group">
                    <select name="status" class="form-control"  onchange="changeStatus(this.value)">
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>SHOW ALL</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Approved</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Unapproved</option>
                    </select> 
                </div> 
            </div>
            <div class="col-md-2" style="margin-left:6px;">
                <a class="btn-refresh" href="#" onclick="clearSearch()"> 
                    <i class="fa fa-refresh" aria-hidden="true"></i>
                </a> 
            </div>
            <div class="d-flex justify-content-end" style="margin-left:18rem;">
                <!-- Excel Export Button -->
                <form action="{{ route('admin.plant.export') }}" method="GET" class="me-2">
                    <input type="hidden" value="{{ $mfs->id }}" name="id">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <button type="submit" class="btnDownloadExcel">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    </button>
                </form>

                <!-- Add Plant Button -->
                <a href="{{ url('add-plant-admin') }}?mfs_id={{ $mfs->id }}" class="addnewplant-btn" style="padding: 9px 21px;">
                    Add Plant
                </a>
            </div>
        </div>
    </div>
</div>
            <div class="added-comminity-body d-none">
                @foreach ($plants as $item)
                @php 
                $image = \App\Models\PlantLogin::where('id',$mfs->id)->first();
                $plant = \App\Models\Plant::where('id',$item->plant_id)->first();
                 @endphp
                    <div class="added-comminity-item">
                        <div class="added-comminity-item-image">
                        @if ($image)
                            <img src="{{ asset('upload/manufacturer-image/' . $image->image) }}" alt="Plant Image">
                        @else
                            <img src="{{ asset('images/default-user-2.png') }}" alt="Default Image">
                        @endif
                        </div>
                        <div class="added-comminity-item-text">
                            <h4>{{ $item->plant_name }}</h4>
                            <div class="added-comminity-location">
                                <img src="{{ asset('admin/images/location.svg') }}">
                                {{ $item->full_address }}
                                <h2
                                class="col-4 mt-2 status-text {{ $plant->status == 1 ? 'status-active' : 'status-inactive' }}">
                                {{ $plant->status == 1 ? 'Active' : 'Inactive' }}
                            </h2>
                            </div>
                            
                            <a class="Viewbtn" href="{{ route('admin.manufracturers.show', encrypt($item->plant_id)) }}">View</a>
                        </div>
                    </div>
                @endforeach
            </div>



            <div class="card-body">
    <div class="ss-card-table">
    <div class="" style="overflow-x: sc !important; width: 100% !important;">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Plant Name</th>
                    <th>Location</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plants as $item)
                @php 
                    $image = \App\Models\PlantMedia::where('plant_id',$item->plant_id)->first();
                    $plant = \App\Models\Plant::where('id',$item->plant_id)->first();
                @endphp
                <tr>
                    <td>
                        <input type="checkbox" name="select_plant[]" value="{{ $item->plant_id }}" id="{{ $item->plant_id }}">
                    </td>
                    <td>
                        <div class="user-profile-item">
                            <div class="user-profile-media">
                                @if ($image)
                                    <img src="{{ asset('upload/manufacturer-image/' . $image->image_url) }}" alt="Plant Image" style="width: 58px; height: auto;">
                                @else
                                    <img src="{{ asset('images/default-user-2.png') }}" alt="Default Image" style="width: 58px; height: auto;">
                                @endif
                            </div>
                            <div class="user-profile-text">
                                <h2>{{ $item->plant_name ?? 'N/A' }}</h2>
                            </div>
                        </div>
                    </td>
                    <td>{{ $item->full_address ?? 'N/A' }}</td>
                    <td>{{ $item->city ?? 'N/A' }}</td>
                    <td>{{ $item->state ?? 'N/A' }}</td>
                    <td>
                        <div class="status-text 
                            @if($item->status == 1 && $item->is_approved == 'N') 
                                status-rejected
                            @elseif($item->status == 1 && $item->is_approved == 'Y') 
                                status-active
                            @elseif($item->status == 0 && $item->is_approved == 'N') 
                                status-rejected
                            @elseif($item->status == 0 && is_null($item->is_approved)) 
                                status-pending
                            @else
                                status-inactive
                            @endif
                        ">
                            @if($item->status == 1 && $item->is_approved == 'N')
                                Unapproved
                            @elseif($item->status == 1 && $item->is_approved == 'Y')
                                Approved
                            @elseif($item->status == 0 && $item->is_approved == 'N')
                                Unapproved
                            @elseif($item->status == 0 && is_null($item->is_approved))
                                Pending
                            @else
                                Pending
                            @endif
                        </div>
                    </td>
                    <td>
                        <a class="action-btn" href="{{ route('admin.manufracturers.show', encrypt($item->plant_id)) }}">
                            <img src="{{ asset('images/arrow-right.svg') }}">
                        </a>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
</div>


        </div>
    </div>
</div>
            </div>
        </div>
    </div>

    <div class="modal lm-modal fade" id="ChangePassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ss-modal-form">
                    <h2>Reset Credentials</h2>
                    <form id="changePasswordForm">
                        @csrf
                        <input type="hidden" id="plant-id" name="plant_id">
                        <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email">Email</label> <!-- Label for the email field -->
                                <input type="email" name="email" id="email" class="form-control" required> <!-- Read-only email input field -->
                            </div>
                        </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="password-wrapper">
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Create New Password" required>
                                        <span toggle="#password" class="eye-toggle fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="password-wrapper">
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm New Password" required>
                                        <span toggle="#password_confirmation" class="eye-toggle fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="save-btn mb-2">Reset</button>
                                    <button type="button" class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="changePasswordMessage"></div>
                </div>
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
                        <p id="delete-message">Are you sure you want to Unapprove these Plants?</p>
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
                        <p id="delete-message">Are you sure you want to Approve these Plants?</p>
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
    <form id="importForm" method="POST" enctype="multipart/form-data" style="display: none;">
  @csrf
  <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" onchange="showModal()" required>
  <input type="hidden" name="mfs_id" id="mfsIdInput" value="{{ $mfs['id'] }}">
</form>


<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
      <div class="ss-modal-delete">
      <div class="ss-modal-delete-icon"><img src="{{asset('images/fileupload.svg')}}"></div>
      <p >Are you sure you want to upload this file?</p>
        <p  id="fileName"></p>
      </div>
      <div class="ss-modal-delete-action">
        <button type="button" class="yes-btn" onclick="submitForm()" style="background-color: var(--pink);">Submit</button>
        <button type="button" class="cancel-btn" data-dismiss="modal" id="cancelBtn">Cancel</button>
      </div>
      </div>
    </div>
  </div>
</div>

<div class="loader-container" id="loader">
                <div class="loader"></div>
            </div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    $('#cancelBtn').on('click', function() {
      $('#fileModal').modal('hide'); // Close the modal with jQuery
      window.location.reload();
    });
  });
</script>
<script>
     function showModal() {
    const fileInput = document.getElementById('fileInput');
    
    const fileName = fileInput.files[0] ? fileInput.files[0].name : 'No file selected';
    
    // Set the file name in the modal
    document.getElementById('fileName').textContent = 'File Selected: ' + fileName;

    // Show the modal
    $('#fileModal').modal('show');
  }


  function submitForm() {
    document.getElementById('loader').style.display = 'block'; // Show loader

    const formData = new FormData(document.getElementById('importForm'));

    fetch("{{ route('plants.importExcel') }}", {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}"
      }
    })
    .then(response => response.json())
    .then(data => {
      document.getElementById('loader').style.display = 'none'; // Hide loader

      if (data.success) {
        Swal.fire({
          title: 'Success!',
          text: data.message,
          icon: 'success',
          confirmButtonText: 'Ok'
        }).then(() => {
          $('#fileModal').modal('hide');
          window.location.reload(); // Reload the page after closing the modal
        });
      } else {
        Swal.fire({
          title: 'Error!',
          text: data.message,
          icon: 'error',
          confirmButtonText: 'Ok'
        });
      }
    })
    .catch(error => {
      document.getElementById('loader').style.display = 'none'; // Hide loader
      console.error('Error:', error);
      Swal.fire({
        title: 'Error!',
        text: 'Error during import.',
        icon: 'error',
        confirmButtonText: 'Ok'
      });
    });
  }

  function cancelUpload() {
    $('#fileModal').modal('hide');
  }


  function changeManu(val) {
    var selectedValue = val;
    var currentUrl = new URL(window.location.href);

    // Add or update the 'run_id' parameter
    currentUrl.searchParams.set('manufacturer_id', selectedValue);
    if (val == 0) {
        currentUrl.searchParams.delete('manufacturer_id');

    }
    // Reload the page with the new URL
    window.location.href = currentUrl.toString();
}
</script>
<script>
    $(document).on('click', '.ChangePasswordbtn', function() {
        var plantId = $(this).data('plant-id');
        var emailId = $(this).data('email-id');

        // Set the plant ID and email in the modal
        $('#plant-id').val(plantId);
        $('#email').val(emailId); // Set the email in the new input field
    });
    function showLoader() {
        document.getElementById('loader').style.display = 'block';
    }

    // Function to hide the loader
    function hideLoader() {
        document.getElementById('loader').style.display = 'none';
    }
    function clearSearch() {
    const url = new URL(window.location.href);
    url.searchParams.delete('search'); // Remove the search parameter
    url.searchParams.delete('status');
    window.location.href = url.toString(); // Reload the page with the updated URL
}
</script>
<script>
    
document.addEventListener("DOMContentLoaded", function() {
    let plantId = null;
    document.querySelectorAll('.ChangePasswordbtn').forEach(function(button) {
        button.addEventListener('click', function() {
            plantId = this.getAttribute('data-plant-id');
            document.getElementById('plant-id').value = plantId; // Set the hidden input field value
        });
    });
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        document.getElementById('loader').style.display = 'block';
        var form = e.target;
        var formData = new FormData(form);
        var manufacturerId = document.getElementById('plant-id').value;
        console.log(manufacturerId);
        var email = document.getElementById('email').value;
        formData.append('email', email);
        const url = `{{ route("admin.updatePassword", ["id" => ":id"]) }}`.replace(':id', plantId);


        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                _token: '{{ csrf_token() }}',
                 'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loader').style.display = 'none';
            var messageContainer = document.getElementById('changePasswordMessage');
            let modal = new bootstrap.Modal(document.getElementById('ChangePassword'));

            if (data.errors) {
                document.getElementById('loader').style.display = 'none';
                messageContainer.innerHTML = '<div class="alert alert-danger">' + data.errors.join('<br>') + '</div>';
            } else {
                document.getElementById('loader').style.display = 'none';
                messageContainer.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                form.reset(); // Optionally reset the form
                
                // Hide the modal after displaying the success message
                setTimeout(() => {
                   modal.hide();
                   window.location.reload();
                }, 2000); // Adjust the delay as needed
            }
        })
        .catch(error => {
            console.error('Error:', error);
            var messageContainer = document.getElementById('changePasswordMessage');
            let modal = new bootstrap.Modal(document.getElementById('ChangePassword'));

            messageContainer.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';

            // Hide the modal after displaying the error message
            setTimeout(() => {
               modal.hide();
            }, 2000); // Adjust the delay as needed
        });
    });
});


document.querySelectorAll('.toggle-password').forEach(function(icon) {
        icon.addEventListener('click', function() {
            var input = document.querySelector(icon.getAttribute('toggle'));
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            showLoader();
            fetch("{{ route('set_status') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    hideLoader(); 
                    if (data.success) {
                        Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Plant(s) have been unapproved successfully!'
                    }).then(() => {
                        location.reload(); // Reload page after alert is closed
                    });
                        location.reload();
                    } else {
                        Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the status.'
                    });
                    }
                })
                .catch(error => {
                hideLoader(); // Hide loader on error
                console.error('Error:', error);
                
                // Error alert
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred.'
                });
            });
    });



    document.getElementById('confirm-activate').addEventListener('click', function(e) {
            e.preventDefault();
            let form = document.getElementById('activate-form');
            let formData = new FormData(form);
            showLoader();
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
                    hideLoader();
                    if (data.success) {
                        Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Plant(s) have been approved successfully!'
                    }).then(() => {
                        location.reload(); // Reload page after alert is closed
                    });
                    } else {
                        Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the status.'
                    });
                    }
                })
                .catch(error => {
                hideLoader(); // Hide loader on error
                console.error('Error:', error);
                
                // Error alert
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred.'
                });
            });
    });
    </script>
    <script>

    function changeStatus(val) {
        var selectedValue = val;
        var currentUrl = new URL(window.location.href);
        
        // Ensure the 0 value is handled properly
        if (selectedValue === '2') {
            // If SHOW ALL is selected, remove the 'status' parameter
            currentUrl.searchParams.delete('status');
        } else {
            // Set the 'status' parameter to the selected value, including 0
            currentUrl.searchParams.set('status', selectedValue);
        }

        // Reload the page with the updated URL
        window.location.href = currentUrl.toString();
    }
    </script>
@endsection



