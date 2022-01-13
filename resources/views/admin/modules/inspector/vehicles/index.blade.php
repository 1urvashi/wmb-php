@extends('admin.layouts.master')
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
                        @can('vehicles_export')
                         <div class="row">

                              <form action="{{url('vehicle/export/csv/'.$id)}}" method="GET">

                             <div class="form-group col-md-2">
                                 <label class="control-label col-sm-2" for="from_date">From</label>
                                     <input type="text" name="from_date" class="filter form-control datepicker" data-date-format='yyyy-mm-dd' id="from_date">
                             </div>
                             <div class="form-group col-md-2">
                                 <label class="control-label col-sm-2" for="to_date">To</label>
                                     <input type="text" name="to_date" class="filter form-control datepicker" data-date-format='yyyy-mm-dd'id="to_date">
                             </div>

                             <div class="form-group col-md-2">
                                 <label class="control-label col-sm-12" for="to_date">Vin Number</label>
                                     <input type="text" name="searchTitle" class="filter form-control" id="searchTitle">
                             </div>

                             <div class="form-group col-md-2">
                                 <label class="control-label col-sm-2">&nbsp;</label>
                                     <input name="exportSubmit"  type="submit" class="form-control btn btn-success btn-sm pull-right" value="Export CSV">
                             </div>
                        </form>
                            {{--<div class="form-group col-md-2">
                                 <label class="control-label col-sm-2">&nbsp;</label>
                                     <input name="exportSubmit" type="submit" class="form-control btn btn-success btn-sm pull-right" value="Export PDF">
                             </div>

                             <div class="col-sm-4 pull-right">
                                  <div class="pull-right">
                                    <a style="margin-top: 30px;" action="{{url('vehicle/export/csv')}}" id="export" class="btn btn-success btn-sm pull-right">EXPORT CSV</a>

                                    <a style="margin-top: 30px;margin-right: 10px;" action="{{url('vehicle/export/pdf')}}" id="export_pdf" class="btn btn-success btn-sm pull-right">EXPORT PDF</a>
                                 </div>
                             </div>--}}
                       </div>
                       @endcan

                <table id="history-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            <th>Vin number</th>
                            <th>Code</th>
                            <th>Uploaded Date</th>
                            <!-- <th>Customer Name</th>
                            <th>Mobile Number</th>
                            <th>Customer Reference</th>
                            <th>Source of Enquiry</th>
                            <th>Email</th>
                            <th>Make</th>
                            <th>Model</th> -->
                            <!-- <th>KM</th> -->
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

</style>
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css"> -->
@endsection
@push('scripts')
<script type='text/javascript'>
$( document ).ready(function() {
     var id = "{{$id}}";
    var table = $('#history-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        bFilter: false,
       //  dom: 'Bfrtip',
       //  buttons: [
       //      'copy', 'csv', 'excel', 'pdf', 'print'
       // ],
        ajax: {
            url: "{{ url('inspectors/vehicle-data/') }}/"+id,
            data: function (d) {
                d.objectName = $('#object_name').val();
                d.searchTitle = $('#searchTitle').val();
                d.from = $('#from_date').val();
                d.to = $('#to_date').val();
            }
        },
        columns: [
            {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
            // {data: 'date', name: 'date'},
            {data: 'name', name: 'name'},
            {data: 'vin', name: 'vin'},
            {data: 'code', name: 'code'},
            // {data: 'customer_name', name: 'customer_name'},
            // {data: 'customer_mobile', name: 'customer_mobile'},
            // {data: 'customer_reference', name: 'customer_reference'},
            // {data: 'source_of_enquiry', name: 'source_of_enquiry'},
            // {data: 'customer_email', name: 'customer_email'},
            // {data: 'makeName', name: 'makeName'},
            // {data: 'modelName', name: 'modelName'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('.filter').on('change keyup',function() {
        table.draw();
    });

    /*$('#export').click(function(event){
         // console.log($(this).attr('action')+'/'+id);
         window.location.href = $(this).attr('action')+'/'+id;
    });
    $('#export_pdf').click(function(event){
         // console.log($(this).attr('action')+'/'+id);
         window.location.href = $(this).attr('action')+'/'+id;
    });*/


})
</script>
<!-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script> -->

@endpush
