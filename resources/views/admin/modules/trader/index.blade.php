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
                    <h2 class="box-title">Trader</h2>
                    <div class="clearfix"></div>
                    @can('traders_create')
                    <a href="{{url('traders/create')}}" class="btn btn-info btn-sm pull-right">ADD NEW TRADER</a>
                    @endcan
                </div>
                <div class="x_content">

                    <div class="box-body">
                        <div class="form-group row">
                            <div style="display: none" class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="dealer">Dealers</label>
                                    <select name="dealer" class="filter form-control" id="dealer">
                                        <option value="0">Select</option>
                                        @foreach($dealers as $dealer)
                                        <option value="{{$dealer->id}}">{{ $dealer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @can('traders_export')
                            <div class="pull-right col-md-4">
                                <a style="margin-top: 30px;" action="{{url('traders/export')}}" id="export" class="btn btn-success btn-sm pull-right">EXPORT
                                    TRADERS</a>
                            </div>
                            @endcan
                        </div>
                        <table id="traders-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Row No</th>
                                    <th>Id</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email Id</th>
                                    {{--<th>Deposit Amount</th>--}}
                                    {{--<th>Last Bid</th>--}}
                                    <th>No of Cashed</th>
                                    <th>Cashed Date</th>
                                    <th>Verify Email</th>
                                    <th>Status</th>
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
    $(document).ready(function () {
        var table = $('#traders-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            // ordering: false,
            ordering: true,
            ajax: {
                url: '{!! route('trader-data') !!}',
                data: function (d) {
                    d.dealer = $('select#dealer').val();
                    d.drms = $('select#drms').val();
                    d.onboarder = $('select#onboarder').val();
                    d.search = $('input[type="search"]').val();
                }
            },
            columns: [{
                    data: 'rownum',
                    name: 'rownum',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'first_name',
                    name: 'first_name'
                },
                {
                    data: 'last_name',
                    name: 'last_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
               /* {
                    data: 'deposit_amount',
                    name: 'deposit_amount'
                },
                {
                    data: 'last_bid',
                    name: 'last_bid'
                },        */
                {
                    data: 'cashed',
                    name: 'cashed',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'cashed_date',
                    name: 'cashed_date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'is_verify_email',
                    name: 'is_verify_email',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        if (data == 1) {
                            var verify_email_text =
                                '<label class="switch"> <input type="checkbox" checked value="0" data-action-type="is_email_verified" data-id=' +
                                row.id + '><span class="slider round"></span></label>'
                        } else {
                            var verify_email_text =
                                '<label class="switch"> <input type="checkbox"  value="1"  data-action-type="is_email_verified" data-id=' +
                                row.id + '><span class="slider round"></span></label>'
                        }
                        return verify_email_text;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        if (data == 1) {
                            var text =
                                '<label class="switch"> <input type="checkbox" checked data-action-type="status" value="0" data-id=' +
                                row.id + '><span class="slider round"></span></label>'
                        } else {
                            var text =
                                '<label class="switch"> <input type="checkbox" value="1" data-action-type="status" data-id=' +
                                row.id + '><span class="slider round"></span></label>'
                        }
                        return text;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        $('.filter').change(function () {
            table.draw();
        })

        $('#export').click(function (event) {
            window.location.href = $(this).attr('action') + '/' + $('#dealer').val()
        })

        $(document).on('change', 'input[type=checkbox]', function () {

            var value = $(this).val();
            var dataId = $(this).data("id");
            var data_action_type = $(this).attr("data-action-type");
            $.ajax({
                url: '{!! url("traders-status") !!}/' + dataId,
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
