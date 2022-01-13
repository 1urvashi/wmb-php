@extends('dealer.layouts.master')
@section('content')
@include('dealer.includes.status-msg')
@php($user = Auth::guard('admin')->user())
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="box-header">
                <h2 class="box-title">Branch Managers</h2>
                <div class="clearfix"></div>
                {{--@if($user->isAllowed('exportManager'))
                <a href="{{url('dealer/branch-managers/export')}}" class="btn btn-success btn-sm pull-right" style="margin-left: 10px;">EXPORT MANAGER</a>
                @endif--}}
                <a href="{{url('dealer/branch-managers/create')}}" class="btn btn-info btn-sm pull-right">ADD NEW MANAGER</a>
            </div>
            <div class="x_content">
                    <div class="box-body">
                <table id="dealers-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            <th>Branch Name</th>
                            <th>Email Id</th>
                            <th>Address</th>
                            <th>Phone</th>
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
    var table = $('#dealers-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        // ordering: false,
        ordering: true,
        ajax: {
            url: '{!! route('dealer-branch-manager-data') !!}',
            data: function (d) {
                d.dealer = $('select#dealer').val();
            }
        },
        columns: [
             {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
             {data: 'name', name: 'name'},
             {data: 'branchName', name: 'branchName'},
             {data: 'email', name: 'email'},
             {data: 'address', name: 'address'},
             {data: 'contact', name: 'contact'},
             {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('.filter').change(function() {
        table.draw();
    })
});
</script>
@endpush
