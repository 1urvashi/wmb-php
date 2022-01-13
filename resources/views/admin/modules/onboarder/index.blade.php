@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
@php($user = Auth::guard('admin')->user())
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="box-header">
                <h2 class="box-title">Onboarder Management</h2>
                <div class="clearfix"></div>
                @can('Onboarder_export')
                <a href="{{url('onboarder-users/export')}}" class="btn btn-success btn-sm pull-right" style="margin-left: 10px;">Export Onboarders</a>
                @endcan
                @can('Onboarder_create')
                <a href="{{url('onboarder-users/create')}}" class="btn btn-info btn-sm pull-right" style="margin-left: 10px;">Add New Onboarder</a>
                @endcan
                @can('Merge_Onboarder-Trader')
                <a href="{{url('merge-onboarder-traders/index')}}" class="btn btn-warning btn-sm pull-right">Merge Trader With Onboarder</a>
                @endcan
            </div>
            <div class="x_content">
               <div class="box-body">
                <table id="traders-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email Id</th>
                            <th>Status</th>
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
<link rel="stylesheet" href="{{ asset('css/custom-checkbox.css') }}">
@endsection
@push('scripts')
<script type='text/javascript'>
$( document ).ready(function() {
    var table = $('#traders-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: true,
        ajax: {
            url: '{!! route('onboarder-data') !!}',
        },
        columns: [
            {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
            {data: 'name', name: 'name', orderable: true, searchable: true},
            {data: 'mobile', name: 'mobile'},
            {data: 'email', name: 'email'},
            {data: 'status', name: 'status',
               render: function (data, type, row) {
                   if (data == 1) {
                        var text =  '<label class="switch"> <input type="checkbox" checked value="0" data-id=' + row.id + '><span class="slider round"></span></label>'
                   } else {
                        var text =  '<label class="switch"> <input type="checkbox" value="1" data-id=' + row.id + '><span class="slider round"></span></label>'
                   }
                   return text;
               }
            },
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('.filter').change(function() {
        table.draw();
    })

    $('#export').click(function(event){
      window.location.href = $(this).attr('action')+'/'+$('#dealer').val()
    })

    $(document).on('change', 'input[type=checkbox]', function() {

         var value = $(this).val();
         var dataId = $(this).data("id");
         $.ajax({
                url: '{!! url("onboarder-users-status") !!}/' + dataId,
                method: 'POST',
                data: {'dataId': dataId, 'dataValue': value},      //POST variable name value
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function () {
                     table.ajax.reload();
                }
            });
    });
})
</script>
@endpush
