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
                <h2 class="box-title">Admin Users</h2>
                <div class="clearfix"></div>
                @can('users_create')
                <a href="{{url('admin-user/create')}}" class="btn btn-info btn-sm pull-right">ADD NEW ADMIN USER</a>
                @endcan
            </div>
            <div class="x_content">
                    <div class="box-body">
                <table id="users-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            <th>Email Id</th>
                            <th>Role</th>
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
    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        ajax: '{!! route('admin-user-data') !!}',
        columns: [
            {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'role', name: 'role'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
})
</script>
@endpush
