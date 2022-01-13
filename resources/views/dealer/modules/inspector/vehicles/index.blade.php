@extends('dealer.layouts.master')
@section('content')
@include('admin.includes.status-msg')
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
                <table id="history-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Uploaded Date</th>
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
     var id = "{{$id}}";
     console.log(id);
    var table = $('#history-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        searching: false,
        ajax: {
            url: "{{ url('dealer/inspectors/vehicle-data/') }}/"+id,
            data: function (d) {
                d.objectName = $('#object_name').val();
                d.dealer = $('#dealer').val();
            }
        },
        columns: [
            {data: 'rownum', name: 'rownum'},
            {data: 'name', name: 'name'},
            {data: 'code', name: 'code'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('.filter').on('change keyup',function() {
        table.draw();
    });

})
</script>
@endpush
