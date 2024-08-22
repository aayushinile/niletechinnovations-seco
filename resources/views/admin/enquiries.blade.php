@extends('admin.layouts')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/home.css') }}">
    <style>
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
    </style>
@endpush
@section('content')
    <div class="body-main-content">
        <div class="ss-card">
            <div class="card-header">
                <h2>Enquiries</h2>
                <div class="search-filter wd6" style="width: 60%">
                    <div class="row g-1">
                        <div class="col-md-4">
                            <form action="">
                                <div class="form-group search-form-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search" value ="{{$search}}">
                                    <span class="search-icon"><img src="images/search-icon.svg"></span>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">

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
                        <div class="col-md-3">
                            <div class="form-group">
                                <select class="form-control" id="manufacturer_id" onchange="changeManu(this.value)">
                                    <option value="0">Show All Plants</option>
                                    @foreach ($pls as $item)
                                    
                                        <option value="{{ $item->plant_name }}"
                                            @if (request()->has('manufacturer_id') && request('manufacturer_id') === $item->id) selected @endif>{{ $item->plant_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <a href="{{ route('admin.enquiries') }}" class="btn-refresh"><i class="fa fa-refresh"
                                        aria-hidden="true" style="margin-top: 12px;"></i></a>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ route('admin.enquiries', array_merge(request()->all(), ['download' => 1])) }}" class="btn-bl" style="background-color:var(--green)">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="ss-card-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Plant Name</th>
                                <th>Co/Retailer Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Location</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$mergedData->isEmpty())
                                @foreach ($mergedData as $data)
                                    <tr>
                                        <td style="width: 8% !important;">{{ date('m-d-Y', strtotime($data->created_at)) }}</td>
                                        <td>{{ $data->plant_name }}</td>
                                        <td>{{ $data->user_name }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td style="width: 12% !important;"> {{ $data->phone_no }}</td>
                                        <td>{{ $data->location }}</td>
                                        <td> {{ substr($data->message, 0, 50) }}{{ strlen($data->message) > 50 ? '...' : '' }}
                                        @if(strlen($data->message) > 30)
                                                    <a class="infoRequestMessage"
                                                        data-bs-toggle="modal"
                                                        href="#infoRequestMessage"
                                                        onclick='GetData("{{ $data->message }}")'
                                                        role="button" style="font-size:15px;"><img src="{{asset('images/info.svg')}}"></a>
                                        @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" style="text-align: center;font-size:15px"> No records found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    @if (method_exists($mergedData, 'hasPages'))
                        <div class="ss-table-pagination">
                            {{ $mergedData->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
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
                                    <p id="message" class="message-text"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            currentUrl.searchParams.set('manufacturer_id', selectedValue);
            if (val == 0) {
                currentUrl.searchParams.delete('manufacturer_id');

            }
            // Reload the page with the new URL
            window.location.href = currentUrl.toString();
        }
    </script>
@endsection
