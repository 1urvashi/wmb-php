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
                </div>
                <div class="x_content">
                    <div class="box-body">
                        <div class="form-group row">
                            <div class="col-md-12 text-right">
                                @can('Push-Notification-Templates_read')
                                <a href="{{ route('notification-templates.index') }}" class="btn btn-warning">Templates</a>
                                @endif
                                <a href="{{ route('notification-history.index') }}" class="btn btn-primary">History</a>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#notifications" data-href="{{  route('notification.create') }}"><i class="fa fa-bell"></i> Send Push</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="dealer">DRMs</label>
                                    <select name="drms" class="filter form-control" id="drms">
                                        <option value="0">Select</option>
                                        @foreach($drmsUsers as $dealer)
                                        <option value="{{$dealer->id}}">{{ $dealer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="platform">Platform(Android/IOS)</label>
                                    <select name="platform" class="filter form-control" id="platform">
                                        <option value="">Select</option>
                                        <option value="">All</option>
                                        <option value="Android">Android</option>
                                        <option value="iOS">IOS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="last_bid">Date of Last Bids</label>
                                    <select name="last_bid" class="filter form-control" id="last_bid">
                                        <option value="">Select</option>
                                        <option value="7">1 Week ago</option>
                                        <option value="14">2 Week ago</option>
                                        <option value="30">1 Month ago</option>
                                        <option value="180">6 Months ago</option>
                                        <option value="-1">No Bids yet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="group">Group</label>
                                    <select name="group" class="filter form-control" id="group">
                                        <option value="0">Select Group</option>
                                        @foreach($trader_groups as $trader_group)
                                        <option value="{{$trader_group->id}}">{{ $trader_group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <table id="traders-table" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Select All <br>
                                    <input type="checkbox" name="select_all[]" class="select_all" value="0">
                                    </th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email Id</th>
                                    <th>Last Bid</th>
                                    <th>DRM</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="notifications" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close modal-dismiss" data-href="{{url('notifications-dismiss')}}" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-bell"></i> Send Push Notfication</h4>
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
<link rel="stylesheet" href="{{ asset('css/custom-checkbox.css') }}">
<link rel="stylesheet" href="{{url('css/loader.css')}}">
<style>
    .count_user { 
        padding: 0 15px;
        font-weight: bold;
        margin-top: 10px;
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
        var selectedCheck = [];
        var table = $('#traders-table').DataTable({
            
            "drawCallback":function(){
                $('.select_all').prop('checked', false);
                $('.trader_check').each(function () {
                    var value = $(this).val();
                    if(selectedCheck.indexOf(value) !== -1) {
                        $(this).prop('checked', true)
                        console.log($(this).html())
                    }
                });
            },
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "pagingType": "full_numbers" ,
            processing: true,
            serverSide: true,
            responsive: true,
            "paging":   true,
            "ordering" : true,
            "scrollY":false,
            "autoWidth": false,
            "serverSide": true,
            "processing": false,
            "info":     true ,
            "deferRender": true,
            "processing": true,
            ordering: true,
            ajax: {
                url: '{!! route('notifications-data') !!}',
                data: function (d) {
                    d.dealer = $('select#dealer').val();
                    d.drms = $('select#drms').val();
                    d.platform = $('select#platform').val();
                    d.last_bid = $('select#last_bid').val();
                    d.group = $('select#group').val();
                    d.search = $('input[type="search"]').val();
                }
            },
            columns: [{
                    data: 'select',
                    name: 'select',
                    orderable: false,
                    searchable: false
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
                    data: 'last_bid',
                    name: 'last_bid'
                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        $('.filter').change(function () {
            selectedCheck = [];
            table.draw();
        })
        
        var oTable = $("#traders-table").dataTable();
        $('#notifications').on('show.bs.modal', function (e) {
            // selectedCheck = [];
            var url = $(e.relatedTarget).attr('data-href');
            // $(".trader_check:checkbox", oTable.fnGetNodes()).each(function () {
            //     var tuisre = $(this).is(":checked");
            //     if (tuisre) {
            //         var no = $(this).val();
            //         selectedCheck.push(no);
            //     }
            // })
            var count_users = selectedCheck.length;
            if(count_users == 0) {
                growl("Please choose at least one Trader to sent notification.", "danger");
                return false;
            }
            $('.count_user').html("Sending to "+count_users+" Traders")
            $.ajax({
                url: url,
                context: this,
                method: "POST",
                dataType: "html",
                data: {traders: JSON.stringify(selectedCheck)},
                beforeSend: function (response) {
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
        $(document).on("click", ".modal-dismiss", function (e) {
            var url = $(this).attr('data-href');
            console.log('url');
            $.ajax({
                url: url,
                context: this,
                method: "POST",
                dataType: "html",
                data: {traders: JSON.stringify(selectedCheck)},
                beforeSend: function (response) {
                    //$('.ajax-loading').show();
                },
                success: function (response) {
                    
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
                    selectedCheck.push($(this).val());
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
        
        
        function notificationValidations(body) {
            var errors = new Object();
            if (body == '') {
                errors.body = "The body field is required.";
            }
            return errors;

        }

        
        $(document).on("submit", "form", function (e) {
            e.preventDefault();
            // selectedCheck = [];
            $('.loading').show();
            // var title = $('#notification_title').val();
            var body = $('#notification_body').val();
            var validate = notificationValidations(body);
            // $(".trader_check:checkbox", oTable.fnGetNodes()).each(function () {
            //     var tuisre = $(this).is(":checked");
            //     if (tuisre) {
            //         var no = $(this).val();
            //         selectedCheck.push(no);
            //     }
            // })
            if ($.isEmptyObject(validate)) {
                $this = this;
                if (selectedCheck.length === 0) {
                    selectedCheck.push("0");
                }
                var form = new FormData($this);
                form.append("user_id", selectedCheck);
                var url = $(this).attr('action');
                $.ajax({
                    url: url,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: form,
                    success: function (responce) {
                        if (responce.status == "400") {
                            $('#notifications').modal('hide');
                            $('.select_all').prop('checked', false);
                            selectedCheck = [];
                            selectedCheck.length = 0;
                            $('#traders-table input[type=checkbox]').prop('checked', false);
                            // table.ajax.reload();
                            $('.loading').hide();
                            growl(responce.message, "success");

                        }
                    }
                });
            } else {
                $(".cls_errror").css("display", 'none');
                $.each(validate, function (key, value) {
                    $('#' + key).css("display", '').html(value);
                });
                $('.loading').hide();
            }
        });
    });
</script>
@endpush