@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Permission Management</h3><br/><br/>
                @can('permission_read')
                {{-- <ahref="url('permissions/create')"class="btnbtn-successbtn-sm">AddNewPermission</a><br/><br/> --}}
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
                            <th>Roles</th>
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
        ajax: '{!! route('permissions.datatable') !!}',
        columns: [
            {data: 'rownum', name: 'rownum'},
            {data: 'name', name: 'name'},
            {data: 'roles', name: 'roles'},
        ]
    });
   });

</script>
@endpush
