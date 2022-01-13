@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Role Management</h3><br/><br/>
                @can('roles_read')
                <a href="{{url('roles/create')}}" class="btn btn-success btn-sm">Add New Role</a><br/><br/>
                @endcan
            </div>
            @include('admin.includes.status-msg')
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="roles">
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

   $('#roles').DataTable({
        processing: true,
        serverSide: true,
          responsive: true,
          ordering: false,
        ajax: '{!! route('roles.datatable') !!}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'label', name: 'label'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
   });

</script>
@endpush
