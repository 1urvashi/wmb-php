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
                    <h2 class="box-title">Push Notifications History</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body">
                        <a href="{{ url('notifications') }}" class="btn btn-primary pull-left text-left btn-sm"><i class="fa fa-backward"></i> Back</a>
                        <br/>
                        <br/>
                        <br/>
                        <table id="traders-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Details</th>
                                    <th>Number of Users</th>
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
<div class="modal fade" id="push_information" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-bell"></i> Push Information</h4>
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
    .modal-dialog {
        width: 900px !important;
    }
    .loading {
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
                url: '{!! route('notifications-history-data') !!}',
            },
            columns: [
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'body',
                    name: 'body'
                },
                {
                    data: 'user_count',
                    name: 'user_count'
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
        var selectedCheck = [];
        $('#push_information').on('show.bs.modal', function (e) {
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
                    var table = $('#push_information #traders-table-lists').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ordering: false,
                        // ordering: true,
                        ajax: {
                            url: "{{ url('notifications-traders-data') }}"+"/"+data_id,
                        },
                        columns: [
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
                            }
                        ]
                    });
                },
                complete: function () {
                    // $('.loading').hide();
                    //$('.ajax-loading').hide();
                }
            });
        });
       
        $(document).on("click", ".select_all", function () {
            if (this.checked) {
                $('.trader_check').each(function () {
                    this.checked = true;
                    selectedCheck = [];
                });
            } else {
                $('.trader_check').each(function () {
                    this.checked = false;
                    selectedCheck = [];
                });
            }
        });
        
        $(document).on("click", ".trader_check", function () {
            var trader_id = $(this).val().toString();
            if ($(this).is(':checked')) {
                // if(jQuery.inArray(trader_id, selectedCheck) == -1) {
                selectedCheck.push(trader_id)
                // }
            } else {
                var index = selectedCheck.indexOf(trader_id);
                if (index > -1) {
                    selectedCheck.splice(index, 1);
                }
            }
            if ($('.trader_check:checked').length == $('.trader_check').length) {
                $('.select_all').prop('checked', true);
            } else {
                $('.select_all').prop('checked', false);
                
            }
            console.log(selectedCheck);
            
        });
        
        
        function notificationValidations(title, body, type) {
            var errors = new Object();
            if (title == '') {
                errors.title = "The title field is required.";
            }
            if (body == '') {
                errors.body = "The body field is required.";
            }
            return errors;

        }
        $('#push_information').on('click', '#confirm', function (e) {
            e.preventDefault();
            $('.loading').show();
            var dataId = $(this).attr('data-id');
            $.ajax({
                url: "{{ url('notification-resend-post') }}"+'/'+dataId,
                processData: false,
                contentType: false,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (responce) {
                    if (responce.status == "400") {
                        $('#push_information').modal('hide');
                        table.ajax.reload();
                        $('.loading').hide();
                        growl(responce.message, "success");

                    }
                }
            });
        })
    });
</script>
@endpush