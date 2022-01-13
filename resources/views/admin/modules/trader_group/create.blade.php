@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">@if(isset($edit)) Edit {{ $edit->name }} Group @else Create Trader Group @endif</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body">
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
                                    <label class="control-label" for="name">Name</label>
                                    <input type="text" name="name" id="group-name" class="form-control" value="@if(isset($edit)){{ $edit->name }}@endif">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label" for="create-btn">&nbsp;</label>
                                    <button type="button" class="add btn btn-success btn-md form-control" id="create-btn" action="@if(isset($edit)) {{route('admin.traders-group.update', $edit->id)}} @else {{ route('admin.traders-group.store') }} @endif">
                                        @if(isset($edit)) 
                                        Update
                                        @else
                                        Create
                                        @endif
                                    </button>
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
        
        var selected = [];
        var selectedCheck = [];
        @if(isset($edit))
        var selectedCheck = [{{ implode(', ', $selectedUser) }}];
        @endif
        var table = $('#traders-table').DataTable({
            "drawCallback":function(){
                $('.select_all').prop('checked', false);
                $('.trader_check').each(function () {
                    var value = $(this).val();
                    console.log(1);
                    if(selectedCheck.indexOf(value) !== -1) {
                        $(this).prop('checked', true)
                        console.log($(this).html())
                    }
                });
            },
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "pagingType": "full_numbers" ,
            "paging":   true,
            "ordering" : true,
            "scrollY":false,
            "autoWidth": false,
            "serverSide": true,
            "searching": true,
            "processing": true,
            "info":     true ,
            "deferRender": true,
            // "processing": true,
            ajax: {
                @if(isset($edit))
                url: '{!! route('trader-list-edit-data', $edit->id) !!}',
                @else
                url: '{!! route('trader-list-data') !!}',
                @endif
                data: function (d) {
                    d.drms = $('select#drms').val();
                    d.search = $('input[type="search"]').val();
                },
                "searchable": true,
                "bProcessing": true,
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
                    data: 'name',
                    name: 'name',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        $('.filter').change(function () {
            table.draw();
        })

        // $('#traders-table').on( 'page.dt', function () {
        //     alert(2);
        //     // var info = table.page.info();
        //     $('.trader_check').each(function () {
        //         var value = $(this).val();
        //         if(selectedCheck.indexOf(value) !== -1) {
        //             alert(1);
        //             // this.checked = true;
        //             $(this).prop('checked', true)
        //             console.log($(this).html())
        //         }
        //     });
        // });
        
        $(document).on("click", ".select_all", function () {
            if (this.checked) {
                $('.trader_check').each(function () {
                    this.checked = true;
                    // if(jQuery.inArray($(this).val(), selectedCheck) == -1) {
                        selectedCheck.push($(this).val());
                    // }
                });
            } else {
                $('.trader_check').each(function () {
                    this.checked = false;
                    selectedCheck = [];
                });
            }
            console.log(selectedCheck);
            
        });

        $(document).on("click", ".trader_check", function () {
            // console.log(a);
            @if(isset($edit))
            var trader_id = parseInt($(this).val());
            @else
            var trader_id = $(this).val().toString();
            @endif
            if ($(this).is(':checked')) {
                if(jQuery.inArray(trader_id, selectedCheck) == -1) {
                    selectedCheck.push(trader_id)
                }
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

        var oTable = $("#traders-table").dataTable();
        var no = [];
        $('#create-btn').on('click', function(e) {
            e.preventDefault();
            var root_location = "{{ url('admin/traders-group') }}";
            // $(".trader_check:checkbox", oTable.fnGetNodes()).each(function () {
            //     var tuisre = $(this).is(":checked");
            //     if (tuisre) {
            //         var no = $(this).val();
            //         selectedCheck.push(no);
            //     }
            // })
            
            
            $('.loading').show();
            var url = $(this).attr('action');
            var name = $('#group-name').val();            
            var trader = selectedCheck;
            $.ajax({
                url: url,
                @if(isset($edit))
                type: 'PUT',
                @else
                type: 'POST',
                @endif
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {name:name, trader:trader},
                success: function (responce) {
                    if (responce.status == "200") {
                        $('.loading').hide();
                        growl(responce.message, "danger");

                    } else {
                        $('.select_all').prop('checked', false);
                        table.ajax.reload();
                        $('.loading').hide();
                        growl(responce.message+' It will redirect within 2 sec', "success");
                        setTimeout(function(){ 
                            // window.location.href = "https://www.example.com";
                            window.location.href = root_location;
                        },3000);
                    }
                }
            });
        });
    });
</script>
@endpush