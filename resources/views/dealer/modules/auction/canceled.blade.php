@extends('dealer.layouts.master')
@section('content')
@include('dealer.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="box-header">
                <h2 class="box-title">{{ucfirst($type)}} Auctions</h2>
                <div class="clearfix"></div>
                <!--a href="{{url('dealer/auction/create')}}" class="btn btn-info btn-sm pull-right">ADD NEW TRADER</a-->
                <input type="hidden" id="type" name="type" value="{{$type}}"/>
            </div>
            <div class="x_content">
                    <div class="box-body">
                <table id="auction-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Minimum Increment</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Base Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type='text/javascript'>
$( document ).ready(function() {
  $(document).on('click','.duplicate-button',function(){
    //  $(this).attr('disabled',true);
  });
    var table = $('#auction-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        ajax: {
            url: '{!! route('dealer-auction-data') !!}',
            data: function (d) {
                d.type = $('#type').val();
                d.search = $('input[type="search"]').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'title', name: 'title'},
            {data: 'start_time', name: 'start_time'},
            {data: 'end_time', name: 'end_time'},
            {data: 'min_increment', name: 'min_increment'},
            {data: 'type', name: 'type'},
            {data: 'status', name: 'status'},
            {data: 'base_price', name: 'base_price'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('.input[type="search"]').change(function() {
        table.draw();
    })
})
</script>
@endpush
