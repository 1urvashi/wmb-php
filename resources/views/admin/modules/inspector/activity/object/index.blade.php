@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Inspector Activities for  {{ $object_name }}</h2>
                    <div class="clearfix"></div>
                    <button onclick="goBack()" class="btn btn-warning btn-sm pull-right"><i class="fa fa-chevron-left"></i> Go Back</button>
                </div>
                <div class="x_content">
                    <div class="box-body">
                        <table id="inspectors-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Session started time</th>
                                    <th>Title</th>
                                    <th>Start end</th>
                                    <th>End time</th>
                                    <th>Spending time</th>
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
    var table = $('#inspectors-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        ajax: {
            url: '{!! route('inspect-activity-object-data', $object_id) !!}',
        },
        columns: [
            {data: 'session_start_time', name: 'session_start_time'},
            {data: 'type', name: 'type'},
            {data: 'start_time', name: 'start_time', orderable: false, searchable: false},
            {data: 'end_time', name: 'end_time', orderable: false, searchable: false},
            {data: 'spending_time', name: 'spending_time', orderable: false, searchable: false}
        ]
    });
    $('.filter').change(function() {
        table.draw();
    })
})
</script>
<script>
    function goBack() {
        window.history.back();
    }
</script>
@endpush