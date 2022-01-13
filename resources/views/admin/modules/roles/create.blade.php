@extends('admin.layouts.master')
@section('content')
<div class="page-title">
    <div class="title_left">
        <!--<h3>Roles Management</h3>-->
    </div>
</div>
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-primary">
            <div class="x_panel">
                <div class="x_title">
                    <div class="box-header">
                        @if(isset($edit))
                        <h2 class="box-title">Edit Role</h2>
                        @else
                        <h2 class="box-title">Add New Role</h2>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="x_content">
                    <br />
                    <form data-parsley-validate class="form-horizontal form-label-left" method="post" action="{{route('roles.store')}}">
                        {!! csrf_field() !!}
                        @include('admin.includes.status-msg')
                        <div class="box-body no-padding">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Name <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="name" required="required" class="form-control col-md-7 col-xs-12"
                                        value=@if(isset($edit)) "{{$edit->label}}" @endif>
                                </div>
                            </div>
                            <div class="panel ">
                                <!-- Default panel contents -->
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="panel panel-default ">
                                            <div class="panel-body">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input id="checkbox-all" class="checkboxRole minimal-red" type="checkbox"
                                                            name="" value="" />
                                                        <label for="checkbox-all" style="margin-left: 5px;">Select All</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @foreach ($permissions as $k => $v)
                                        @if(is_array($v))
                                        <div class="panel panel-default" style="clear: both;">
                                            <div class="panel-body">
                                                <div class="panel-heading"><b class="text-left">{{ucfirst($k)}}</b>
                                                    <label for="{{ $k }}-all" class="outer-label text-right pull-right">
                                                        <input type="checkbox" class="{{ $k }}-all minimal-red box-select" id="{{ $k }}-all">
                                                        Select All
                                                    </label>
                                                </div>
                                                @foreach ($v as $k1 => $v1)
                                                <div class="col-md-3 permission-blocks">
                                                    {{ Form::checkbox('permission[]', $k1, null,
                                                    ['class'=>'checkboxRole minimal-red ' .$k] ) }}
                                                    {{ Form::label($v1, ucfirst($v1)) }}
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @else
                                        <div class="col-md-3">
                                            {{ Form::checkbox('permission[]', $v, null, ['class'=>'checkboxRole
                                            minimal-red'] ) }}
                                            {{ Form::label($k, ucfirst($k)) }}
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" style="text-align: center;">
                                    {{--<button type="reset" class="btn btn-primary">Cancel</button>--}}
                                    <button type="submit" class="btn btn-primary">@if(isset($edit)) {{"Update"}} @else
                                        {{"Submit"}} @endif</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style>
    .permission-blocks, .outer-label {
            margin-bottom: 25px;
        }
    </style>
@section('scripts')
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type='text/javascript'>
    //select all checkboxes
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
        checkboxClass: 'icheckbox_minimal-red',
        radioClass: 'iradio_minimal-red'
    });

    $('#checkbox-all').on('ifChecked', function (event) {
        $('input[type="checkbox"].minimal-red').iCheck('check');
        triggeredByChild = false;
    });

    $('.box-select').on('ifChecked', function (event) {
        var outer = $(this).parent().parent().parent().parent().find('input[type="checkbox"].minimal-red');
        $(outer).iCheck('check');
        triggeredByChild = false;
    });

    $('.box-select').on('ifUnchecked', function (event) {
        var outer = $(this).parent().parent().parent().parent().find('input[type="checkbox"].minimal-red');
        if (!triggeredByChild) {
            $(outer).iCheck('uncheck');
        }
        triggeredByChild = false;
    });

    $('#checkbox-all').on('ifUnchecked', function (event) {
        if (!triggeredByChild) {
            $('input[type="checkbox"].minimal-red').iCheck('uncheck');
        }
        triggeredByChild = false;

    });

    $('input[type="checkbox"].minimal-red').on('ifUnchecked', function (event) {
        triggeredByChild = true;
        $('#checkbox-all').iCheck('uncheck');
    });

    $('input[type="checkbox"].minimal-red').on('ifChecked', function (event) {
        if ($('input[type="checkbox"].minimal-red').filter(':checked').length == $('.check').length) {
            $('#checkbox-all').iCheck('check');
        }
    });
</script>
@endsection