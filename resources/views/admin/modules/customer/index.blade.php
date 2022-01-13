@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="box-header">
                <h2 class="box-title">Customers</h2>
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

                <table id="traders-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Reference</th>
                            <th>Date</th>
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
        ordering: false,
        ajax: '{!! route('customer-data') !!}',
        columns: [
            {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
            {data: 'customer_name', name: 'customer_name'},
            {data: 'mobile', name: 'mobile'},
            {data: 'email', name: 'email'},
            {data: 'customer_reference_number', name: 'customer_reference_number'},
            {data: 'created_at', name: 'created_at'}
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
                url: '{!! url("traders-status") !!}/' + dataId,
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
