@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Models Management</h3><br/><br/>

                <div class="btn-group">
                    @can('model_create')
                      <a href="{{url('model/create')}}" class="btn btn-success btn-sm" style="margin-right: 15px; border-radius: 3px">Add New</a>
                      @endcan
                      @can('model_import')
                      &nbsp; <a href="{{url('importModel')}}" class="btn btn-success btn-sm" style="border-radius: 3px">Import Data</a>
                      @endcan
                    </div>
            </div>
            @include('admin.includes.status-msg')
            <!-- /.box-header -->
            <div class="box-body">
                <div class="form-group row">
                    <div class="form-group col-md-4">
                       <label class="control-label" for="attributeset">Make</label>
                           <select name="make" class="filter form-control" id="make" >
                           <option value="">Choose Make</option>
                           @foreach($makes as $make)
                               <option value="{{$make->id}}">{{$make->name}}</option>
                           @endforeach
                           </select>
                   </div>
                </div>
                <table class="table table-striped table-bordered table-hover" id="models">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Model</th>
                            <th>Make</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>

            </div>

            <!-- /.box-body -->
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {

       var table = $('#models').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ordering: false,
            ajax: {
                url: '{!! route("model-data") !!}',
                data: function (d) {
                    d.make = $('select#make').val();
                    d.search = $('input[type="search"]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'mkname', name: 'mkname'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        $('.filter').change(function() {
            table.draw();
        });
    });

</script>
@endpush
