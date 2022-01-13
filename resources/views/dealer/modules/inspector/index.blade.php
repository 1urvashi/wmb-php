@extends('dealer.layouts.master')
@section('content')
@include('dealer.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="box-header">
                <h2 class="box-title">Inspector</h2>
                <div class="clearfix"></div>
                <a href="{{url('dealer/inspectors/create')}}" class="btn btn-info btn-sm pull-right">ADD NEW INSPECTOR</a>
            </div>
            <div class="x_content">
                    <div class="box-body">
                <table id="inspectors-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            <th>Email Id</th>
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
    $('#inspectors-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        ajax: '{!! route('dealer-inspect-data') !!}',
        columns: [
            {data: 'rownum', name: 'rownum'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
})
</script>
@endpush
