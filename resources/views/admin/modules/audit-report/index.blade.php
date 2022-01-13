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
                <h2 class="box-title">Audit Report</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                    <div class="box-body">
                <table id="users-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Email Id</th>
                            <th>Last Login Time</th>
                            <th>IP Address of Last Login</th>
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
    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        iDisplayLength: 25,
        ajax: '{!! route('audit.datatable') !!}',
        columns: [
            {
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                searchable: false
            },
            {data: 'email', name: 'email'},
            {data: 'time', name: 'time'},
            {data: 'ip', name: 'ip'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
})
</script>
@endpush
