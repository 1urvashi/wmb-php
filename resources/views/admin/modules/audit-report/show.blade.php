@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <a href="{{ url('/audit-report') }}" title="Back"><div class="btn btn-warning btn-xs"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</div></a>
                    <div class="clearfix"></div>
                    <div class="text-center"><h2 class="box-title">{{$user->email}}</h2></div>
                </div>
                <div class="x_content">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="dealer">From Date</label>
                                    <input type="text" name="from_date" class="form-control datepicker" data-date-format='yyyy-mm-dd' id="from_date">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="inspector_sources">To Date</label>
                                    <input type="text" name="to_date" class="form-control datepicker" data-date-format='yyyy-mm-dd'id="to_date">
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label" for="inspector_sources">&nbsp;</label>
                                <div class="form-group">
                                    <button class="filter btn btn-primary"><i class="fa fa-search"></i> Search</button>  
                                </div>
                            </div>
                        </div>
                        <table id="users-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Row No</th>
                                    <th>Date and Time</th>
                                    <th>IP Location</th>
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
    $(document).ready(function () {
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ordering: false,
            searching: false,
            iDisplayLength: 25,
            ajax: {
                url: '{!! route('audit.user.datatable') !!}',
                data: function (d) {
                    d.userId = '{{$user->id}}';
                    d.from = $('#from_date').val();
                    d.to = $('#to_date').val();
                }
            },
            columns: [
                {
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    searchable: false
                },
                {data: 'time', name: 'time'},
                {data: 'ip', name: 'ip'},
            ]
        });

        $(document).on('click', '.filter', function () {
            table.draw();
        });
    });
</script>
@endpush
