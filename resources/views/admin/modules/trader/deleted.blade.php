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
                    <h2 class="box-title">Deleted Traders</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <div class="box-body">

                        {{--<div class="form-group row">

                        </div>--}}


                        <table id="traders-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Row No</th>
                                    <th>Id</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email Id</th>
                                    <th>Deposit Amount</th>
                                    <th>Last Bid</th>
                                    {{--<th>DRM/Head DRM</th>
                                    <th>Branch</th>--}}
                                    <th>Deleted Date</th>
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
                url: '{!! route('trader-deleted-data') !!}',
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
                {
                    data: 'deposit_amount',
                    name: 'deposit_amount'
                },
                {
                    data: 'last_bid',
                    name: 'last_bid'
                },
                {{--{
                    data: 'dmr_id',
                    name: 'dmr_id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'dealer_id',
                    name: 'dealer_id',
                    orderable: false,
                    searchable: false
                },--}}
                {
                    data: 'deleted_at',
                    name: 'deleted_at',
                    orderable: false,
                    searchable: false
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
            $.ajax({
                url: '{!! url("traders-status") !!}/' + dataId,
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
    })
</script>
@endpush
