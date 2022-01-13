@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
@php($user = Auth::guard('admin')->user())
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="box box-success">
        <div class="x_panel">
            <div class="x_content">

                    <div class="box-body">

                <div class="form-group row">
                    @if($user->type != config('globalConstants.TYPE.DRM'))
                     <form id="mergeForm" class="mergeForm" method="post" action="{{url('merge-traders/post')}}">
                          <div class="col-md-4">
                               <div class="form-group">
                                  <label class="control-label" for="dealer">DRMs</label>
                                      <select name="drms" class="filter form-control" id="drms" >
                                      <option>Select</option>
                                      @foreach($drmsUsers as $dealer)
                                      <?php
                                      $otext = $dealer->name;
                                      
                                      switch($dealer->type){
                                          case $gType['DRM']:
                                              $otext .= ' (DRM)';
                                              break;
                                          case $gType['HEAD_DRM']:
                                              $otext .= ' (HEAD OF DRM)';
                                              break;
                                          default:
                                              break;
                                      }
                                      ?>
                                          <option value="{{$dealer->id}}">
                                              {{ $otext }}
                                          </option>
                                      @endforeach
                                      </select>
                                      <div id="message"></div>
                              </div>
                          </div>
                          <div class="col-md-4">
                               <div class="form-group">
                                     <label class="control-label" style="width: 100%;">&nbsp;</label>
                                    <button type="submit" class="btn btn-success">Merge</button>
                               </div>
                          </div>
                     </form></br>

                     @endif
                </div>
                <table id="traders-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email Id</th>
                            <th>Deposit Amount</th>
                            <th>Last Bid</th>
                            <th>Action <input type="checkbox" class="select_all" /> </th>
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
<style media="screen">
     #message {
          color: #000;
         font-weight: bold;
         margin-top: 5px;
         font-style: italic;
     }
</style>
<link rel="stylesheet" href="{{url('css/loader.css')}}">
<link rel="stylesheet" href="{{ asset('css/custom-checkbox.css') }}">
@endsection
@push('scripts')
<script type="text/javascript" src="{{ asset('js/myadmin.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.js"></script>
<script type='text/javascript'>
$( document ).ready(function() {
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
            url: '{!! route('merge-trader-data') !!}'
        },
        columns: [
            {data: 'rownum', name: 'rownum', orderable: false, searchable: false},
            {data: 'first_name', name: 'first_name'},
            {data: 'last_name', name: 'last_name'},
            {data: 'email', name: 'email'},
            {data: 'deposit_amount', name: 'deposit_amount'},
            {data: 'last_bid', name: 'last_bid'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    $('#export').click(function(event){
      window.location.href = $(this).attr('action')+'/'+$('#dealer').val()
    })

    // Check

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

    // End Check


    var frm = $('#mergeForm');

    frm.submit(function (e) {
         $('.loading').show();
        e.preventDefault();
        var count_users = selectedCheck.length;
        if(count_users == 0) {
            growl("Please choose at least one Trader to merge.", "danger");
            $('.loading').hide();
            return false;
        }

        $.ajax({
            type: frm.attr('method'),
            url: frm.attr('action'),
            data: {merge:selectedCheck, drm:$('#drms').val()},
            success: function (data) {
                    if(data.status == true) {
                        $('#message').html(data.msg);
                        $('.loading').hide();
                        selectedCheck = [];
                        selectedCheck.length = 0;
                        table.ajax.reload();
                    } else {
                        $('#message').html(data.msg);
                        $('.loading').hide();
                    }
            },
            error: function (data) {
                 $('.loading').hide();
                $('#message').html('Oops something went wrong message');
            },
        });
    });


})
</script>
@endpush
