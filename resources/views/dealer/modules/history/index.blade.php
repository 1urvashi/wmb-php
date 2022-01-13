@extends('dealer.layouts.master')
@section('content')
@include('admin.includes.status-msg')
@php($user = Auth::guard('admin')->user())
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel history-wrapper">
            <div class="box-header">
                <h2 class="box-title">History</h2>
                <div class="clearfix"></div>
                {{--
                <button action="{{url('history/export')}}" id="export-btn" class="btn btn-success btn-sm pull-right">EXPORT AUCTIONS HISTORY</button>
                --}}
            </div>
            <div class="x_content">
                    <div class="box-body">
                    <div class="row">
                                   <div class="form-group col-md-3">
                                      <label class="control-label" for="trader">Status</label>
                                         <select name="status" class="filter form-control" id="status">
                                              <option value="">Select</option>
                                              <option value="3">Auction completed</option>
                                              <option value="8">Cashed</option>
                                              <option value="7">Sold</option>
                                              <option value="9">Stopped while under auction</option>
                                              <option value="10">Cancelled after completion</option>
                                              <option value="12">Cancelled</option>
                                         </select>
                                  </div>
                               {{-- </div>
                                <div class="row">--}}
                                     <div class="form-group col-md-3">
                                        <label class="control-label" for="start_time">Start Date</label>
                                             <input type="text" name="start_time" class="filter form-control datepicker" id="start_time" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-3">
                                       <label class="control-label" for="start_time">End Date</label>
                                            <input type="text" name="end_time" class="filter form-control datepicker" id="end_time" autocomplete="off">
                                   </div>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-12">


                <table id="history-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Title</th>
                            <th>Watch Name</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Bid Amount</th>
                            <th>Bid Time</th>
                            <th>Min Increment</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Bid Owner</th>
                            <th>Auction Details</th>
                        </tr>
                    </thead>

                </table>
                </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<style media="screen">
     .history-wrapper {
          overflow: auto;
     }
</style>
@endsection
@push('scripts')
<script type='text/javascript'>
var trader = getUrlVars()["trader"];
if(trader){
    $('#trader option[value="'+trader+'"]').attr('selected',true).trigger('change');
}
$( document ).ready(function() {
    var table = $('#history-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        ajax: {
            url: '{!! route('deals-history-data') !!}',
            data: function (d) {
                d.dealer = {{Auth::guard('dealer')->user()->id}};
                d.status = $('select#status').val();
                d.start_time = $('#start_time').val();
                d.end_time = $('#end_time').val();
                d.search = $('input[type="search"]').val();
            }
        },
        columns: [
            {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
            {data: 'title', name: 'title'},
            {data: 'objects_id', name: 'objects_id'},
            {data: 'start_time', name: 'start_time'},
            {data: 'end_time', name: 'end_time'},
            {data: 'bid_price', name: 'bid_price'},
            {data: 'last_bid_date', name: 'last_bid_date'},
            {data: 'min_increment', name: 'min_increment'},
            {data: 'type', name: 'type'},
            {data: 'status', name: 'status'},
            {data: 'bid_owners', name: 'bid_owners'},
            {data: 'auction_detail', name: 'auction_detail'}
        ]
    });
    $('#dealer').change(function(){
        $.ajax({
            type: "get",
            url: "{{ url('traderlist') }}",
            data: 'dealer=' + $('#dealer').val(),
            beforeSend: function () {
                $('#trader').empty();
                $('#trader').append('<option value="0">Select</option>');
            },
            success: function (data) {
                var response = JSON.parse(data);
                if(response.status == 'success'){
                    traders = response.data;
                    $.each(traders,function(k,v){
                        $('#trader').append('<option value="'+v.id+'">'+v.first_name+'</option>');
                    })
                }
            }
        });
    });
    $('.filter').change(function() {
        table.draw();
    });


    $('#export-btn').on('click', function(e) {
        e.preventDefault();
       var url = $(this).attr('action');
       var dealer = $('#dealer').val();
       var trader = $('#trader').val();
       var status = $('#status').val();
       var start_time = $('#start_time').val();
       var end_time = $('#end_time').val();
       var complete = url+'?dealer='+dealer+'&&trader='+trader+'&&status='+status+'&&start_time='+start_time+'&&end_time='+end_time;
       console.log(complete);

       window.location.href = complete
        // $.ajax({
        //     type: "GET",
        //     url: url,
        //     data: {"dealer": dealer, "trader": trader, "status": status, "start_time": start_time}
        // });
    })


})
$('.datepicker').datepicker({ autoclose: true });
// $(function () {
//     $("#start_time").datepicker({
//         // numberOfMonths: 2,
//         onSelect: function (selected) {
//             var dt = new Date(selected);
//             dt.setDate(dt.getDate() + 1);
//             $("#end_time").datepicker("option", "minDate", dt);
//         }
//     });
//     $("#end_time").datepicker({
//         // numberOfMonths: 2,
//         onSelect: function (selected) {
//             var dt = new Date(selected);
//             dt.setDate(dt.getDate() - 1);
//             $("#start_time").datepicker("option", "maxDate", dt);
//         }
//     });
// });
</script>
@endpush
