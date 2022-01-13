@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Inspector</h2>
                    <div class="clearfix"></div>
                    @can('inspectors_export')
                    <a href="{{url('inspectors/export')}}" class="btn btn-success btn-sm pull-right"
                        style="margin-left: 10px;">EXPORT INSPECTORS</a>
                    @endcan
                    @can('inspectors_create')
                    <a href="{{url('inspectors/create')}}" class="btn btn-info btn-sm pull-right" style="margin-left: 10px;">ADD NEW INSPECTOR</a>
                    @endcan
                    @can('inspectors_trashed-read')
                    <a href="{{ route('inspectors.trashed') }}" class="btn btn-danger btn-sm pull-right" style="margin-left: 10px;">TRASHED INSPECTORS</a>
                    @endcan
                </div>
                <div class="x_content">

                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
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
                            <div class="form-group col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="inspector_sources">Inspector Sources</label>
                                    <select name="inspector_sources" class="filter form-control" id="inspector_sources">
                                        <option value="0">Select</option>
                                        @foreach($inspector_sources as $values)
                                        <option value="{{$values->id}}">{{ $values->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <table id="inspectors-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Row No</th>
                                    <th>Name</th>
                                    <th>Email Id</th>
                                    <th>Source</th>
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
    $(document).ready(function () {
        var table = $('#inspectors-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ordering: false,
            ajax: {
                url: '{!! route('inspect-data') !!}',
                data: function (d) {
                    d.dealer = $('select#dealer').val();
                    d.inspector_sources = $('select#inspector_sources').val();
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'source_id',
                    name: 'source_id'
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
    })
</script>
@endpush