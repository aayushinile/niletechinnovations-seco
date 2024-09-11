

@extends('manufacturer.layouts')
<style>
    .buttons-container {
    display: flex !important;
    flex-direction: row;
    align-items: center;
    gap: 10px; /* Adjust the gap as needed */
}

.buttons-container a {
    margin-right: 0; /* Remove any additional margin */
}

a.addnewplant-btn {
    background: var(--pink);
    width: 100%;
    color: var(--white);
    padding: 8px 15px;
    border-radius: 5px;
    font-size: 14px;
    box-shadow: 0 4px 10px #5f0f5845;
    display: inline-block;
    position: relative;
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
.btn-bl {
    outline: none;
    width: 100%;
    padding: 12px 11px;
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
                <div class="ss-heading-section">
                    <h2>Manage Manufacturer/Plant</h2>
                    <div class="search-filter wd40">
                    <form action="{{route('manufacturer.manage-locations')}}" method="POST">
                    @csrf
                    @php
                        $user = Auth::user();
                        $plant_login = \App\Models\PlantLogin::where('id',$user->id)->first();
                        $add_plant = $media = \App\Models\Plant::where('manufacturer_id',$user->id)->count();
                    @endphp
                    @if($plant_login->plant_type ==  'plant_rep')
                        <div class="row g-1 justify-content-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <a href="{{ url('add-plant') }}" class="addnewplant-btn" style="padding: 9px 21px;">Add Plant</a>
                                </div> 
                            </div>
                        </div>
                    @else 
                    <div class="row g-1">
                            <div class="col-md-5">
                                <div class="form-group search-form-group">
                                    <input type="text"  name="search" value="{{ $search ? $search : '' }}"
                                    class="form-control" placeholder="Search">
                                    <span class="search-icon"><img src="{{asset('images/search-icon.svg')}}"></span>
                                </div> 
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <a href="{{route('manufacturer.manage-locations')}}" class="btn-refresh"><i
                                            class="fa fa-refresh" aria-hidden="true" style="margin-top: 12px;"></i></a>
                                </div>
                            </div>
                            
                            <div class="col-md-1">
                                <div class="form-group">
                                    <button type="submit" class="btn-search"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            @if ($plants->isNotEmpty())
                            <div class="col-md-1">
                                <a href="{{ route('manufacturer.manage-locations', array_merge(request()->all(), ['download' => 1])) }}" class="btn-bl" style="background-color:var(--green)">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                </a>

                            </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <a href="{{ url('add-plant') }}" class="addnewplant-btn" style="padding: 9px 21px;">Add Plant</a>
                                </div> 
                            </div>
                        </div>
                            
                    @endif
                    </form>  
                </div>
            </div>
                <div class="listed-plants-section">

                    <!-- <div class="">
                        <div id="plants-slider" class="owl-carousel owl-theme">
                            <div class="item">
                                <div class="listed-plants-slider-media">
                                    <a href="images/1.jpg" data-fancybox data-caption="Single image">
                                        <img src="images/1.jpg" />
                                    </a>
                                </div>
                            </div>
                            <div class="item"><h4>2</h4></div>
                            <div class="item"><h4>3</h4></div>
                        </div>
                    </div> -->

                    @if(session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="row">
                    
                    @if ($plants->isEmpty())
                    <div class="col-12 d-flex justify-content-center">
                        <div class="manage-tour-card"  style="text-align: center;margin-top:3rem;padding:0.3rem;color:#000">
                            No records Found
                        </div>
                    </div>
                    @else
                    @foreach($plants as $item)
                    @php
                    $media = \App\Models\PlantMedia::where('plant_id',$item->id)->first();
                    @endphp
                        <div class="col-md-4">
                            <div class="listed-plants-item" id="plant-{{ $item->id }}">
                                <div class="listed-plants-item-head">
                                    <div class="listed-plants-item-profile">
                                        <div class="listed-plants-item-image">
                                        @if(!empty($media))
                                        <img src="{{ asset('upload/manufacturer-image/'.$media->image_url) }}" alt="plant_media">
                                        @else 
                                        <img src="{{ asset('images/default-plant-image.png') }}">
                                        @endif 
                                            
                                        </div>
                                        <div class="listed-plants-item-content">
                                            <h2>{{ $item->plant_name }}</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="listed-plants-item-media">
                                    @if(!empty($media))
                                    <img src="{{ asset('upload/manufacturer-image/'.$media->image_url) }}" alt="plant_media">
                                    @else 
                                    <img src="{{ asset('images/default-plant-image.png') }}">
                                    @endif 
                                </div> 

                                <div class="listed-plants-item-descr">
                                    <div class="listed-plants-location">
                                        <img src="{{ asset('images/location.svg') }}">{{$item['full_address']}}
                                    </div>
                                    <div class="listed-plants-item-action buttons-container"  style="cursor: pointer;">
                                        <a class="deletebtn" data-bs-toggle="modal" data-bs-target="#deleteplants" data-plant-id="{{ $item['id'] }}" data-plant-name="{{ $item['plant_name'] }}">Delete</a>
                                        <a class="editbtn" href="{{ url('edit-plant/' .$item['id']) }}" style="margin-top: 6px;">Edit</a>
                                        <a class="viewbtn" href="{{ url('view-plant/' . $item->id) }}" style="padding: 6px 29px;">View</a>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                        @if (method_exists($plants, 'hasPages'))
                        <div class="ss-table-pagination" style="margin-left: 22px;">
                            <ul class="ss-pagination">
                                @if ($plants->onFirstPage())
                                    <li class="disabled" id="example_previous">
                                        <a href="#" aria-controls="example" data-dt-idx="0" tabindex="0"
                                            class="page-link">Prev</a>
                                    </li>
                                @else
                                    <li id="example_previous">
                                        <a href="{{ $plants->previousPageUrl() }}" aria-controls="example" data-dt-idx="0"
                                            tabindex="0" class="page-link">Prev</a>
                                    </li>
                                @endif

                                @foreach ($plants->getUrlRange(1, $plants->lastPage()) as $page => $url)
                                    <li class="{{ $plants->currentPage() == $page ? 'active' : '' }}">
                                        <a href="{{ $url }}" aria-controls="example"
                                            data-dt-idx="{{ $page }}" tabindex="0"
                                            class="page-link">{{ $page }}</a>
                                    </li>
                                @endforeach

                                @if ($plants->hasMorePages())
                                    <li class="next" id="example_next">
                                        <a href="{{ $plants->nextPageUrl() }}" aria-controls="example" data-dt-idx="7"
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


<!-- Delete listed plants -->
<div class="modal ss-modal fade" id="deleteplants" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ss-modal-delete">
                    <div class="ss-modal-delete-icon"><img src="{{asset('images/delete.svg')}}"></div>
                    <h2>Delete listed plant</h2>
                    <p id="delete-message">Are you sure you want to delete this plant from the listing?</p>
                    
                        <form id="delete-form" action="{{ url('delete-plant') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" id="plant-id" name="plant_id">
                            
                            <input type="hidden" id="plant-name" name="plant_name">
                            <div class="ss-modal-delete-action">
                            <button type="submit" class="yes-btn" >Yes, Delete</button>
                            <button type="button" class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Listen for click on delete button
        $('.deletebtn').click(function () {
            var plantId = $(this).data('plant-id');
            var plantName = $(this).data('plant-name'); // If you want to show plant name in delete confirmation message
            $('#plant-id').val(plantId);
            $('#delete-message').text('Are you sure you want to delete "' + plantName + '" from the listing?'); // Update delete confirmation message
        });

        // Submit delete form
        $('#delete-form').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            $.ajax({
                type: 'DELETE',
                url: url,
                data: form.serialize(),
                success: function (response) {
                    // Handle success (e.g., close modal, update UI)
                    $('#deleteplants').modal('hide');
                    location.reload();
                },
                error: function (error) {
                    // Handle errors (e.g., show error message)
                    console.error('Error deleting plant:', error);
                }
            });
        });
    });
</script>
@endsection