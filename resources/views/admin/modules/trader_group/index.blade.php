@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Trader Groups</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <a href="{{ route('admin.traders-group.create') }}" class="btn btn-md btn-success">Add New</a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <table id="traders-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>No of Traders</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="loading" style="display:none;">Loading&#8230;</div>
<link rel="stylesheet" href="{{ asset('css/custom-checkbox.css') }}">
@endsection
@push('scripts')
<script type="text/javascript" src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/myadmin.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.js"></script>
<script type='text/javascript'>
    $(document).ready(function () {
        var table = $('#traders-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            // ordering: false,
            ordering: true,
            ajax: {
                url: '{!! route('trader-group-data') !!}',
            },
            columns: [
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'user_count',
                    name: 'user_count'
                },
                
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });
</script>
@endpush