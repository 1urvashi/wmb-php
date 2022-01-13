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
                <h2 class="box-title">Dealers</h2>
                <div class="clearfix"></div>
                @can('branches_export')
                <a href="{{url('dealers/export')}}" class="btn btn-success btn-sm pull-right" style="margin-left: 10px;">EXPORT DEALERS</a>
                @endcan
                @can('branches_create')
                <a href="{{url('dealers/create')}}" class="btn btn-info btn-sm pull-right">ADD NEW DEALER</a>
                @endcan
            </div>
            <div class="x_content">
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
                            <th>Email Id</th>
                            <th>Address</th>
                            <th>Phone</th>
                            {{-- <th>Verify Email</th>
                            <th>Status</th> --}}
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
<link rel="stylesheet" href="{{ asset('css/custom-checkbox.css') }}">
@endsection
@push('scripts')
<script type='text/javascript'>
$( document ).ready(function() {
    $('#dealers-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        ajax: '{!! route('dealer-data') !!}',
        columns: [
            {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'address', name: 'address'},
            {data: 'contact', name: 'contact'},
            // {
            //     data: 'is_verify_email',
            //     name: 'is_verify_email',
            //     orderable: false,
            //     searchable: false,
            //     render: function (data, type, row) {
            //         if (data == 1) {
            //             var verify_email_text =
            //                 '<label class="switch"> <input type="checkbox" checked value="0" data-action-type="is_email_verified" data-id=' +
            //                 row.id + '><span class="slider round"></span></label>'
            //         } else {
            //             var verify_email_text =
            //                 '<label class="switch"> <input type="checkbox"  value="1"  data-action-type="is_email_verified" data-id=' +
            //                 row.id + '><span class="slider round"></span></label>'
            //         }
            //         return verify_email_text;
            //     }
            // },
            // {
            //     data: 'status',
            //     name: 'status',
            //     orderable: false,
            //     searchable: false,
            //     render: function (data, type, row) {
            //         if (data == 1) {
            //             var text =
            //                 '<label class="switch"> <input type="checkbox" checked data-action-type="status" value="0" data-id=' +
            //                 row.id + '><span class="slider round"></span></label>'
            //         } else {
            //             var text =
            //                 '<label class="switch"> <input type="checkbox" value="1" data-action-type="status" data-id=' +
            //                 row.id + '><span class="slider round"></span></label>'
            //         }
            //         return text;
            //     }
            // },
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    
    $(document).on('change', 'input[type=checkbox]', function () {

        var value = $(this).val();
        var dataId = $(this).data("id");
        var data_action_type = $(this).attr("data-action-type");
        $.ajax({
            url: '{!! url("dealers-status") !!}/' + dataId,
            method: 'POST',
            data: {
                'dataId': dataId,
                'dataValue': value,
                'data_action_type': data_action_type
            }, //POST variable name value
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function () {
                table.ajax.reload();
            }
        });
    });
})
</script>
@endpush
