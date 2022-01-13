@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Inspector Activities - Watches</h2>
                    <div class="clearfix"></div>
                    <button onclick="goBack()" class="btn btn-warning btn-sm pull-right"><i class="fa fa-chevron-left"></i> Go Back</button>
                </div>
                <div class="x_content">
                    <div class="box-body">
                        <table id="inspectors-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
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
    var table = $('#inspectors-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        ajax: {
            url: '{!! route('inspect-activity-data', $inspector_id) !!}',
        },
        columns: [
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
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