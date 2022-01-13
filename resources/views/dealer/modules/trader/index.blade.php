@extends('dealer.layouts.master')
@section('content')
@include('dealer.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="box-header">
                <h2 class="box-title">Trader</h2>
                <div class="clearfix"></div>
                <a href="{{url('dealer/traders/create')}}" class="btn btn-info btn-sm pull-right">ADD NEW TRADER</a>
            </div>
            <div class="x_content">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                    @endif
                    <div class="box-body">
                      <div class="form-group">
                         <div class="pull-left">
                           <a style="margin-top: 30px; margin-bottom: 30px;" href="{{url('dealer/traders/export')}}" id="export" class="btn btn-success btn-sm pull-left">EXPORT TRADERS</a>
                        </div>
                      </div>
                <table id="traders-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>First Name</th>
                            <th>Last Name</th>
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
        // ordering: false,
        ordering: true,
        ajax: {
            url: '{!! route('dealer-trader-data') !!}'
        },
        columns: [
            {data: 'rownum', name: 'rownum'},
            {data: 'first_name', name: 'first_name'},
            {data: 'last_name', name: 'last_name'},
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
   });

   $(document).on('change', 'input[type=checkbox]', function() {

        var value = $(this).val();
        var dataId = $(this).data("id");
        $.ajax({
               url: '{!! url("dealer/traders-status") !!}/' + dataId,
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
