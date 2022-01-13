@extends('admin.layouts.master')
@section('content')
@php($user = Auth::guard('admin')->user())
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Download Report</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            @include('admin.includes.status-msg')
            <form role="form" enctype="multipart/form-data" method="get" action="{{ url('getAuctions') }}">
                {{-- {{ csrf_field() }} --}}
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>Date <span class="req">*</span></label>
                                <input type="text" class="form-control datepicker-download" id="date" name="date" value="{{ old('date') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .datepicker table tr td.disabled, .datepicker table tr td.disabled:hover {
        color: #ddd !important;
    }
</style>
@endsection
@push('scripts')
<script type='text/javascript'>
    $(document).ready(function(){
        var today = new Date();
        $('.datepicker-download').datepicker({
            format: 'dd-mm-yyyy',
            autoclose:true,
            endDate: "today",
            maxDate: today
        }).on('changeDate', function (ev) {
                $(this).datepicker('hide');
            });


        $('.datepicker-download').keyup(function () {
            if (this.value.match(/[^0-9]/g)) {
                this.value = this.value.replace(/[^0-9^-]/g, '');
            }
        });
    });
</script>
@endpush