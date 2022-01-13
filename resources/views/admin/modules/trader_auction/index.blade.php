@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
@php($user =Auth::guard('admin')->user())
@php($datas = new \App\Auction())
@php($types = $datas->getAuctionTypes())
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="box-header">
                <h2 class="box-title">Trader Auctions</h2>
                <div class="clearfix"></div>
                <button class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#filter-modal"><i class="fa fa-pencil-square-o"></i> Filter</button>
            </div>
            <div class="x_content" style="overflow: auto;">
                    <div class="box-body">
                        <table id="auction-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Title</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Base Price</th>
                                    <th>Bid Owner</th>
                                    <th>Bid Price</th>
                                </tr>
                            </thead>

                        </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<!-- The Modal -->
<div class="modal" id="filter-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Filter</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    @if($user->type == config('globalConstants.TYPE.ADMIN') || $user->type == config('globalConstants.TYPE.SUPER_ADMIN'))
                    {{-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="drm">DRMs</label>
                            <select name="drm" id="drm" class="filter form-control">
                                <option value="0">All</option>
                                @foreach($drm_users as $drm_user)
                                <option value="{{ $drm_user->id }}">{{ $drm_user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="traders">Traders</label>
                            <select name="traders" id="traders" class="filter form-control">
                                <option value="0">All</option>
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">From</label>
                            <input type="text" name="from" id="from" value="" class="datepicker form-control filter">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">To</label>
                            <input type="text" name="to" id="to" value="" class="datepicker form-control filter">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Type</label>
                            <select name="type" id="type" class="filter form-control">
                                <option value="0">All</option>
                                @foreach($types as $key => $v)
                                <option value="{{ $key }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Bid Price</label>
                            <input type="text" name="bid_price" id="bid_price" value="" class="slider form-control filter" data-slider-min="0" data-slider-max="{{ $latestBidPrice }}" data-slider-step="5" data-slider-value="[0, {{ $latestBidPrice }}]" data-slider-orientation="horizontal" data-slider-selection="before" data-slider-tooltip="show" data-slider-id="blue">
                        </div>
                    </div>
                </div>
                
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="filter">Filter</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
           </div>

        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-slider/slider.css') }}">
@endsection
@push('scripts')

<script type='text/javascript'>

$( document ).ready(function() {
    $('.slider').slider();
    var table = $('#auction-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        searchable: false,
        ajax: {
            url: '{!! route('trader-auction-data') !!}',
            data: function (d) {
                d.type = $('#type').val();
                d.drm = $('select#drm').val();
                d.traders = $('#traders').val();
                d.from = $('#from').val();
                d.to = $('#to').val();
                d.bid_price = $('#bid_price').val();
                d.type = $('#type').val();
                d.search = $('input[type="search"]').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'title', name: 'title'},
            {data: 'start_time', name: 'start_time'},
            {data: 'end_time', name: 'end_time'},
            {data: 'type', name: 'type'},
            {data: 'status', name: 'status'},
            {data: 'base_price', name: 'base_price'},
            {data: 'bid_owner', name: 'bid_owner'},
            {data: 'price', name: 'price'},
        ]
    });
    $('#filter').on('click', function() {
        table.draw();
        $('#filter-modal').modal('hide');
    });

    $('select#drm').change(function(){
         $.ajax({
            url: "{{ route('admin-get-traders') }}",
            type: 'GET',
            cache: false,
            data: {drm_id: $('select#drm').val()},
            success: function(html){
                $("select#traders").html(html);
            }
        });
    });
 
    $.ajax({
    url: "{{ route('admin-get-traders') }}",
    type: 'GET',
    cache: false,
    //  data: {drm_id: $('select#drm').val()},
    success: function(html){
        $("select#traders").html(html);
    }
});
 

    $('.datepicker').datepicker({ autoclose: true });


    $("#to").change(function () {
        var startDate = document.getElementById("from").value;
        var endDate = document.getElementById("to").value;
    
        if ((Date.parse(endDate) < Date.parse(startDate))) {
            alert("End date should be greater than Start date");
            document.getElementById("to").value = "";
        }
    });

})
</script>
@endpush
