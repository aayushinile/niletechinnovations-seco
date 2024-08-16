
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
</style>
@section('content')
            <div class="body-main-content">
                <div class="overview-section">
                    <div class="row">
                        <div class="col-md-5">
                        <a href="{{route('manufacturer.enquiry')}}">
                            <div class="overview-enquiries-card">
                                <div class="overview-enquiries-text">
                                    <h1>New Enquiries</h1>
                                      <p>{{$new_enquiries->count()}}</p>
                                </div>
                                <div class="overview-enquiries-img"><img src="{{asset('images/enquiries1.svg')}}" height="170" alt=""></div>
                            </div>
                        </a>
                        </div>
                        <div class="col-md-7">
                            <div class="overview-card">
                                <div class="overview-card-head">
                                    <div class="overview-location-item">
                                        @php
                                        $type = \App\Models\PlantLogin::where('id',$user->id)->first();
                                        @endphp
                                        @if($type->plant_type = 'corp_rep')
                                        <a href="{{route('manufacturer.manage-locations')}}">
                                        @endif
                                        <div class="overview-location-content">
                                            <div class="overview-location-content-text">
                                                <p>Total listed Plant/Manufacturer</p>
                                                <h2>{{$count}}</h2>
                                            </div>    
                                            <div class="overview-location-content-icon">
                                                <img src="{{asset('images/location-icon.svg')}}">
                                            </div>    
                                        </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="overview-card-body">
                                    <div class="row g-1">
                                    @foreach ($locationCounts as $location => $details)
                                    <div class="col-md-3">
                                        <div class="overview-location-item">
                                            <div class="overview-location-content">
                                                <div class="overview-location-content-text">
                                                    <h2>{{ $details['city'] }}, {!! $details['state'] !!}</h2>
                                                </div>
                                                <div class="overview-location-content-icon">
                                                    <img src="{{ asset('images/location-icon.svg') }}" alt="Location Icon">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                    </div>
                                </div>
                            </div>    
                        </div>
                        
                    </div>
                </div>
                <div class="lp-card">
                    <div class="card-header d-flex">
                        <h2>Enquiries</h2>
                        <div class="search-filter wd8">
                          <a href="{{route('manufacturer.enquiry')}}" ><span class="new-msg-text" style="font-size: 16px;border-radius: 5px;padding: 5px 12px;line-height: 2;">View all</span> </a>
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
                                        <th>Name</th>
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
                                            <td><span class="sno">{{ $s_no }}</span></td>
                                            
                                            <td><span class="new-msg-text">New</span> {{ date('m/d/Y', strtotime($enquiry->created_at)) }}</td>
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
                                                        role="button" style="font-size:15px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                                        </svg></a>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="switch-toggle">
                                                    <div class="">
                                                        <label class="toggle" for="myToggleClass_{{ $s_no }}">
                                                            <input class="toggle__input myToggleClass_" name="status" data-id="{{ $enquiry->enquiry_id }}" type="checkbox" id="myToggleClass_{{ $s_no }}">
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
                                        <td colspan="8" style="text-align: center;font-size:15px"> No Records Found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="ss-table-pagination">
                                {{ $new_enquiries->links() }}
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


    </script>
@endsection
                               