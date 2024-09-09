
@extends('manufacturer.layouts')
<style>
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
a.btn-refresh {
    width: 36px;
    border: none;
    display: inline-block;
    height: 40px;
    text-align: center;
    background: var(--pink);
    color: #fff;
    line-height: 45px;
    border-radius: 5px;
}
button.btn-search {
    width: 36px;
    border: none;
    display: inline-block;
    height: 40px;
    text-align: center;
    background: var(--pink);
    color: #fff;
    line-height: 45px;
    border-radius: 5px;
}

.ss-modal-message {
    position: relative;
}

.ss-modal-message .message-text {font-size: 16px; margin: 0; color: var(--gray); font-weight: 300; padding: 0; }
.ss-modal-message .btn-close {position: absolute; right: 0;}
.message-modal-card img {
    height: 64px;
    width: 64px;
    margin-bottom: 5px;
}
.message-modal-card {
    text-align: center;
}
.pagination{
    --bs-pagination-active-bg: var(--pink) !important;
}
.btn-bl {
    outline: none;
    width: 100%;
    padding: 10px 15px;
    text-align: center;
    display: inline-block;
    color: var(--white);
    font-size: 14px;
    font-weight: 600;
    border-radius: 5px;
    border: none;
    box-shadow: 0px 8px 13px 0px rgba(0, 0, 0, 0.05);
    background: var(--pink);
    margin-bottom: 5px;
}
</style> 
@section('content')

            <div class="body-main-content">
                <div class="lp-card">
                    <div class="card-header">
                        <h2>Inquiries({{$count}})</h2>
                        <div class="search-filter wd6">
                        <form action="{{route('manufacturer.enquiry')}}" method="POST">
                        @csrf
                            <div class="row g-1">
                                <div class="col-md-2">
                                    <div class="form-group search-form-group">
                                        <input type="text"name="search" value="{{ $search ? $search : '' }}" class="form-control" placeholder="Search">
                                        <span class="search-icon"><img src="{{asset('images/search-icon.svg')}}"></span>
                                    </div> 
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="date" name="date" value="{{ $date ? $date : '' }}" class="form-control">
                                    </div> 
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control" id="manufacturer_id" onchange="changeManu(this.value)">
                                            <option value="0">Show All Plants</option>
                                            @foreach ($plants as $item)
                                            
                                                <option value="{{ $item->plant_name }}"
                                                    @if (request()->has('manufacturer_id') && request('manufacturer_id') === $item->plant_name) selected @endif>{{ $item->plant_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group">
                                        <select name="status_filter" class="form-control"  onchange="changeStatus(this.value)">
                                        <option value="">SHOW ALL</option>
                                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Mark As Read</option>
                                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Mark As Unread</option>
                                        </select> 
                                    </div> 
                                </div>

                                <div class="col-md-1">
                                    <div class="form-group">
                                        <a href="{{route('manufacturer.enquiry')}}" class="btn-refresh"><i
                                                class="fa fa-refresh" aria-hidden="true" style="margin-top: 12px;"></i></a>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <button type="submit" class="btn-search"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('manufacturer.enquiry', array_merge(request()->all(), ['download' => 1])) }}" class="btn-bl" style="background-color:var(--green)">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    </a>

                                </div>
                            </div> 
                        </form>  
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="ss-card-table table-responsive">
                            <table class="table">
                                <thead>
                                @php
                                    $type = \App\Models\PlantLogin::where('id',$user->id)->first();
                                    @endphp
                                    <tr>
                                        <th>S.no</th>
                                        <th>Date</th>
                                        @if($type->plant_type == 'corp_rep')
                                        <th>Plant Name</th>
                                        @endif
                                        <th>CO/Retailer Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(!$new_enquiries->isEmpty())
                                <?php $s_no = 1; ?>
                                    @foreach ($new_enquiries as $index => $enquiry)
                                    @php
                                     $enq = \App\Models\ContactManufacturer::where('id',$enquiry->enquiry_id)->first();
                                     $plant = \App\Models\Plant::where('id',$enq->plant_id)->first();
                                    @endphp
                                        <tr>
                                            <td><span class="sno">{{ $index + 1 }}</span></td>
                                            <td>@if($enquiry->status == 0)<span class="new-msg-text">New</span>@endif {{ date('m/d/Y', strtotime($enquiry->created_at)) }}</td>
                                            @if($type->plant_type == 'corp_rep')
                                            <td>{{ $plant->plant_name ?? 'N/A'}}</td>
                                            @endif
                                            <td>{{ $enquiry->enquiry_name }}</td>
                                            <td>{{ $enquiry->enquiry_mail }}</td>
                                            <td>{{ $enquiry->enquiry_phone }}</td>
                                            <td>
                                                {{ strlen($enquiry->message) > 30 ? substr($enquiry->message, 0, 30) . '...' : $enquiry->message }}
                                                @if(strlen($enquiry->message) > 30)
                                                    <a class="infoRequestMessage"
                                                        data-bs-toggle="modal"
                                                        href="#infoRequestMessage"
                                                        onclick='GetData("{{ $enquiry->message }}")'
                                                        role="button" style="font-size:15px;"><img src="{{asset('images/info.svg')}}"></a>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="switch-toggle">
                                                    <div class="">
                                                        <label class="toggle" for="myToggleClass_{{ $s_no }}">
                                                            <input class="toggle__input myToggleClass_" 
                                                                @if ($enquiry->status == 1) checked @endif 
                                                                name="status" 
                                                                data-id="{{ $enquiry->enquiry_id }}" 
                                                                type="checkbox" 
                                                                id="myToggleClass_{{ $s_no }}">
                                                            <div class="toggle__fill"></div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $s_no++; ?>
                                    @endforeach
                                    @else 
                                    <tr>
                                        <td colspan="8" style="text-align: center;font-size:15px"> No records found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="ss-table-pagination">
                                {{ $new_enquiries->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>   
            </div>
            <div class="modal ss-modal fade" id="infoRequestMessage" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content ">
                        <div class="modal-body">
                            <div class="ss-modal-message">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div class="message-modal-card">
                                    <img src="{{asset('images/info.svg')}}">
                                    <p id="message" class="message-text"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
        function GetData(message) {
            document.getElementById("message").innerText =
                message;
        }
    </script>
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
            <script>
       $(document).ready(function() {
    $('.myToggleClass_').on('change', function() {
        var newStatus = this.checked ? '1' : '0';
        var request_id = $(this).attr("data-id");
        var baseUrl = "{{ url('/') }}";
        console.log(request_id);

        console.log('Sending AJAX request with:', {
            request_id: request_id,
            status: newStatus,
            _token: '{{ csrf_token() }}'
        });

        $.ajax({
            url: baseUrl+'/toggleRequestStatus',
            type: 'POST',
            data: {
                request_id: request_id,
                status: newStatus,
                _token: '{{ csrf_token() }}'
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


        function changeStatus(val) {
            var selectedValue = val;
            var currentUrl = new URL(window.location.href);

            // Add or update the 'run_id' parameter
            currentUrl.searchParams.set('status', selectedValue);
            if (val == 0) {
                currentUrl.searchParams.delete('status');

            }
            // Reload the page with the new URL
            window.location.href = currentUrl.toString();
        }
    </script>
    @endsection
                               