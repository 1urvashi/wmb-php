@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
@php($user = Auth::guard('admin')->user())
@php($exportVehicle = !is_null($user) && $user && Gate::allows('vehicles-under-auction_export') ? 1 : 0)

<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="box-header">
                <h2 class="box-title">Watches</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-5">
                               <label class="control-label" for="dealer">Dealers</label>
                                   <select name="dealer" class="filter form-control" id="dealer" >
                                   <option value="0">Select</option>
                                   @foreach($dealers as $dealer)
                                       <option value="{{$dealer->id}}">{{ $dealer->name }}</option>
                                   @endforeach
                                   </select>
                           </div>
                            {{--  <div class="form-group col-md-5">
                               <label class="control-label" for="start_time">Vin Number</label>
                                <input type="text" name="object_name" class="filter form-control" id="object_name">
                           </div>  --}}
                           <div class="pull-right col-md-2">
                             @if($exportVehicle)
                             <a style="margin-top: 30px;" action="{{url('objects/auction/export')}}" id="export" class="btn btn-success btn-sm pull-right">EXPORT WATCHES</a>
                             @endif
                          </div>
                       </div>
                <table id="history-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            {{--  <th>Vin Number</th>  --}}
                            <th>Code</th>
                            <th>Uploaded Date</th>
                            {{-- <th>Suggested Amount</th> --}}
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
<style type="text/css">
    #history-table_filter {
        display: none;
    }
</style>
@endsection
@push('scripts')
<script type='text/javascript'>
$( document ).ready(function() {

    var table = $('#history-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        searching: false,
        ajax: {
            url: '{!! route('admin-object-data') !!}',
            data: function (d) {
                d.objectName = $('#object_name').val();
                d.dealer = $('#dealer').val();
            }
        },
        columns: [
            {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {{--  {data: 'vin', name: 'vin'},  --}}
            {data: 'code', name: 'code'},
            {data: 'created_at', name: 'created_at'},
            // {data: 'suggested_amount', name: 'suggested_amount'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('.filter').on('change keyup',function() {
        table.draw();
    });

    $(document).on('click','.duplicate-button',function(){
     //   $(this).attr('disabled',true);
    });

    $('#export').click(function(event){
      window.location.href = $(this).attr('action')+'/'+$('#dealer').val();
    })

})
</script>
@endpush
