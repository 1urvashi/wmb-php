@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Sales Type Management</h3><br /><br />
                @can('priceType_salestype-create')
                <a href="{{url('sales-types/create')}}" class="btn btn-success btn-sm">Add New</a><br /><br />
                @endcan
            </div>
            @include('admin.includes.status-msg')
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            @if(Gate::allows('priceType_salestype-create'))
                            <th>Status</th>
                            @endif
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>

            </div>

            <!-- /.box-body -->
        </div>
    </div>
</div>
<!-- The Modal -->
<div class="modal" id="duplicate-model">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create Duplicate Sales Type</h4>
            </div>
            <div class="modal-body">

            </div>
            <!-- Modal footer -->


        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('css/custom-checkbox.css') }}">
<style>
    .errorDiv {
        color: red;
    }
</style>
@endsection
@push('scripts')
<script type="text/javascript" src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/myadmin.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.js"></script>
<script>
    $(document).ready(function () {
        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            // ordering: false,
            ordering: true,
            ajax: '{!! route('sales-types-data') !!}',
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                @if(Gate::allows('priceType_salestype-create'))
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        if (data == 1) {
                            var text =
                                '<label class="switch"> <input type="checkbox" checked value="0" data-id=' +
                                row.id + '><span class="slider round"></span></label>'
                        } else {
                            var text =
                                '<label class="switch"> <input type="checkbox" value="1" data-id=' +
                                row.id + '><span class="slider round"></span></label>'
                        }
                        return text;
                    }
                },
                @endif
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $(document).on('change', 'input[type=checkbox]', function () {

            var value = $(this).val();
            var dataId = $(this).data("id");
            $.ajax({
                url: '{!! url("sales-types") !!}/' + dataId,
                method: 'POST',
                data: {
                    'dataId': dataId,
                    'dataValue': value
                }, //POST variable name value
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function () {
                    table.ajax.reload();
                }
            });
        });

        $('#duplicate-model').on('show.bs.modal', function (e) {
             var url = $(e.relatedTarget).attr('data-href');
             $.ajax({
                 url: url,
                 context: this,
                 method: "GET",
                 dataType: "html",
                 beforeSend: function () {

                 },
                 success: function (response) {
                     $(this).find('.modal-body').html(response);
                 },
                 complete: function () {
                     //$('.ajax-loading').hide();
                 }
             });
         });

         $('#duplicate-model').on('submit', 'form', function (e) {
            e.preventDefault();
            var action = $(this).attr('action');
            var _token = $("input[name='_token']").val();
            var name = $("input[name='name']").val();
            var type_id = $("input[name='type_id']").val();
            $.ajax({
                url: action,
                type:'POST',
                data: {_token:_token, name:name, type_id:type_id},
                success: function(response) {
                     if(response.success == true) {
                        table.ajax.reload();
                        $('#duplicate-model').modal('hide');
                        growl(response.msg, "success");
                     } else {
                        $('#duplicate-model').modal('hide');
                        growl(response.msg, "danger");
                     }
               },
                error: function (response) {
                     var errors = jQuery.parseJSON(response.responseText);
                     console.log(errors);

                     $.each(errors, function (key, val) {
                          $("#" + key + '_error').html(val).show();
                     });
                }
            });
        });
    });
</script>
@endpush
