@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Push Notifications Templates</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body">
                        @if(session()->has('selected_traders'))
                        <h3 class="text-center">Select Template to send to {{ count(session()->get('selected_traders')) }} Traders</h3>
                        @endif
                        <div class="form-group row">
                            
                            <div class="col-md-12 text-right">
                                <a href="{{ url('notifications') }}" class="btn btn-primary pull-left text-left btn-sm"><i class="fa fa-backward"></i> Back</a>
                                @if(session()->has('selected_traders'))
                                <a href="{{ route('notifications-templates-cancel') }}" class="btn  btn-sm btn-md btn-danger">Cancel</a>
                                @else
                                    @can('Push-Notification-Templates_create')
                                    <button type="button" class="btn btn-md btn-success" data-toggle="modal" data-target="#push_templates" data-href="{{route('notification-templates.create')}}"><i class="fa fa-plus"></i> Create New</button>
                                    @endcan
                                @endif                                
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <table id="traders-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Details</th>
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
<div class="modal fade" id="push_templates" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-send"></i> Push Notification Template</h4>
            </div>
            <div class="count_user"></div>
            <div class="modal-body">
                
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="loading" style="display:none;">Loading&#8230;</div>
<link rel="stylesheet" href="{{url('css/loader.css')}}">
<link rel="stylesheet" href="{{ asset('css/custom-checkbox.css') }}">
<style>
    .loading {
        z-index: 99999 !important;
    }
    .errorDiv {
        color: red;
    }
    .alert.alert-danger.animated.fadeInDown {
        z-index: 99999 !important;
    }
</style>
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
                url: '{!! route('notifications-templates-data') !!}',
            },
            columns: [{
                    data: 'rownum',
                    name: 'rownum'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'body',
                    name: 'body'
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

        $('#push_templates').on('show.bs.modal', function (e) {
            var url = $(e.relatedTarget).attr('data-href');
            var data_id = $(e.relatedTarget).attr('data-id');
            $.ajax({
                url: url,
                context: this,
                method: "GET",
                dataType: "html",
                beforeSend: function () {
                    //$('.ajax-loading').show();
                },
                success: function (response) {
                    $(this).find('.modal-body').html(response);
                },
                complete: function () {
                    // $('.loading').hide();
                    //$('.ajax-loading').hide();
                }
            });
        });

        $(document).on("click", "#template_send", function (e) {
            e.preventDefault();
            count_trader = "{{ count(session()->get('selected_traders')) }}";
            console.log(count_trader);
            bootbox.confirm("Sending the template new trader to "+count_trader+" Traders", function (result) {
                if (result === false) {
                    return;
                }
                $('.errorDiv').html('');
                $('.loading').show(); 
                var action = $(this).attr('action');
                var content_id = $('#id_field').val();
                var _token = $("input[name='_token']").val();
                var method = $('#method').val();
                var title = $("input[name='title']").val();
                var body = $("#body").val();
                $.ajax({
                    url: "{{ url('template-send-post/') }}/"+content_id,
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    data: {_token:_token, title:title, body:body},
                    success: function(response) {
                        if(response.success == true) {
                            table.ajax.reload();
                            $('#push_templates').modal('hide');
                            $('.loading').hide();
                            growl(response.msg, "success");
                            location.reload();
                        } else {
                            growl(response.msg, "danger");
                            table.ajax.reload();
                            $('.loading').hide();
                        }
                    },
                    error: function (response) {
                        var errors = jQuery.parseJSON(response.responseText);
                        $.each(errors, function (key, val) {
                            $("#" + key + '_error').html(val).show();
                        });
                        $('.loading').hide();
                    }
                });
            });
        });


        //add Edit
        $('#push_templates').on('submit', 'form#resoucesData', function (e) {
            e.preventDefault();           
            $('.errorDiv').html('');
            $('.loading').show(); 
            var action = $(this).attr('action');
            var _token = $("input[name='_token']").val();
            var method = $('#method').val();
            var title = $("input[name='title']").val();
            var body = $("#body").val();
            $.ajax({
                url: action,
                method: method,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                data: {_token:_token, title:title, body:body},
                success: function(response) {
                    if(response.success == true) {
                        table.ajax.reload();
                        $('#push_templates').modal('hide');
                        $('.loading').hide();
                        growl(response.msg, "success");
                    } else {
                        growl(response.msg, "danger");
                        table.ajax.reload();
                        $('.loading').hide();
                    }
                },
                error: function (response) {
                    var errors = jQuery.parseJSON(response.responseText);
                    $.each(errors, function (key, val) {
                        $("#" + key + '_error').html(val).show();
                    });
                    $('.loading').hide();
                }
            });
        });
        @if (Session::has('growl'))
           @if (is_array(Session::get('growl')))
           growl("{!! Session::get('growl')[0] !!}", "{{ Session::get('growl')[1] }}");
   @else
           growl("{{Session::get('growl')}}");
   @endif
           @endif
    });
</script>
@endpush