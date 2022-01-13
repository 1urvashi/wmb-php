@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
@php($user = Auth::guard('admin')->user())
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="box-header">
                <h2 class="box-title">Dealer Managers</h2>
                <div class="clearfix"></div>
                @can('branch-managers_export')
                <a href="{{url('branch-managers/export')}}" class="btn btn-success btn-sm pull-right" style="margin-left: 10px;">EXPORT MANAGER</a>
                @endcan
                @can('branch-managers_create')
                <a href="{{url('branch-managers/create')}}" class="btn btn-info btn-sm pull-right">ADD NEW MANAGER</a>
                @endcan
            </div>
            <div class="x_content">

                 <div class="form-group">
                     <div class="col-md-4">
                          <div class="form-group">
                             <label class="control-label" for="dealer">Dealers</label>
                                 <select name="dealer" class="filter form-control" id="dealer" >
                                 <option value="0">Select</option>
                                 @foreach($branches as $dealer)
                                     <option value="{{$dealer->id}}">{{ $dealer->name }}</option>
                                 @endforeach
                                 </select>
                         </div>
                     </div>
                </div>
                 {{--
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
               --}}
                    <div class="box-body">
                <table id="dealers-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Name</th>
                            <th>Branch Name</th>
                            <th>Email Id</th>
                            <th>Address</th>
                            <th>Phone</th>
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
    var table = $('#dealers-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        // ordering: false,
        ordering: true,
        ajax: {
            url: '{!! route('branch-manager-data') !!}',
            data: function (d) {
                d.dealer = $('select#dealer').val();
            }
        },
        columns: [
             {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
             {data: 'name', name: 'name'},
             {data: 'branchName', name: 'branchName'},
             {data: 'email', name: 'email'},
             {data: 'address', name: 'address'},
             {data: 'contact', name: 'contact'},
             {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('.filter').change(function() {
        table.draw();
    })
});
</script>
@endpush
