@extends('dealer.layouts.master')
@section('content')
{{--@include('dealer.includes.status-msg')--}}
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
          <div class="box-header">
              <h2 class="box-title">Watches</h2>
              <a style="margin-left:10px;" href="{{url('dealer/objects/create')}}" class="btn btn-info btn-sm">Add New</a>
              <div class="clearfix"></div>
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
                        <div class="row">
                            <div class="form-group col-md-6">
                               <label class="control-label col-sm-3" for="start_time">Watch Name</label>
                                <input type="text" name="object_name" class="filter form-control" id="object_name">
                            </div>
                       </div>
                <table id="objects-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Uploaded Date</th>
                            {{--  <th>Suggested Amount</th>  --}}
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
    var table = $('#objects-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        searching: false,
        ajax: {
            url: '{!! route('dealer-no-object-data') !!}',
            data: function (d) {
                d.objectName = $('#object_name').val();
            }
        },
        columns: [
            {data: 'rownum', name: 'rownum'},
            {data: 'name', name: 'name'},
            {data: 'code', name: 'code'},
            {data: 'created_at', name: 'created_at'},
            {{--  {data: 'suggested_amount', name: 'suggested_amount'},  --}}
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('.filter').on('change keyup',function() {
        table.draw();
    })
})
</script>
@endpush
