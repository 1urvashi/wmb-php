@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Brand Management</h3><br/><br/>
                @can('make_create')
                <a href="{{url('make/create')}}" class="btn btn-success btn-sm">Add New</a><br/><br/>
                @endcan
            </div>
            @include('admin.includes.status-msg')
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="makes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
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

   $('#makes').DataTable({
        processing: true,
        serverSide: true,
          responsive: true,
          ordering: false,
        ajax: '{!! route('make-data') !!}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
   });

</script>
@endpush
