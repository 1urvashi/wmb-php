@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Profit Margin Management</h3><br/><br/>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRange" data-href="{{route('form-load')}}">
                   Add New Range
                 </button>
            </div>
            @include('admin.includes.status-msg')
            <!-- /.box-header -->
            <div class="box-body">
                 <table class="table table-striped table-bordered table-hover" id="data-table">
                    <thead>
                         <tr>
                             <th>From</th>
                             <th>To</th>
                             <th>Profit Margin</th>
                             <th>Action</th>
                         </tr>
                    </thead>
                 </table>
            </div>

            <!-- /.box-body -->
        </div>
    </div>
</div>
<!-- The Modal -->
  <div class="modal" id="addRange">
    <div class="modal-dialog">
      <div class="modal-content">
           <input type="hidden" name="sales_type_id" value="{{$id}}">

        <!-- Modal Header -->
        <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Profit Margin</h4>
          <div class="successMessage"></div>
          <div class="errorMessage"></div>

        </div>
        <div class="modal-body">

        </div>
        <!-- Modal footer -->


      </div>
    </div>
  </div>
  <div class="loading" style="display:none;">Loading&#8230;</div>
<link rel="stylesheet" href="{{ asset('css/custom-checkbox.css') }}">
<link rel="stylesheet" href="{{url('css/loader.css')}}">
@endsection

     <style media="screen">
          .selectProfitType {
               margin-top: 20px;
               width: 100%;
               float: left;
          }
          label.error, .errorDiv {
               float: left;
               width: 100%;
               color: red;
               /* text-transform: uppercase; */
          }
          .select-profit {
               float: left;
               width: 100%;
               border: 3px solid #DDD;
               padding: 25px 10px;
          }
          .successMessage {
               color: green;
               font-weight: bold;
          }
          .errorMessage {
               color: red;
               font-weight: bold;
          }
          .close {
               font-size: 35px !important;
          }
     </style>
@push('scripts')
<script type="text/javascript" src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/myadmin.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.js"></script>
<script>
    $(document).ready(function () {
         var table = $('#data-table').DataTable({
             processing: true,
             serverSide: true,
             responsive: true,
             // ordering: false,
             ordering: true,
             ajax: '{!! route('profit-data', $id) !!}',
             columns: [
                 {data: 'range_from', name: 'range_from'},
                 {data: 'range_to', name: 'range_to'},
                 {data: 'profit_status', name: 'profit_status'},
                 {data: 'action', name: 'action', orderable: false, searchable: false}
             ]
         });

         $('#addRange').on('show.bs.modal', function (e) {
              $('.loading').show();
             var url = $(e.relatedTarget).attr('data-href');
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
                     $('.loading').hide();
                 },
                 complete: function () {
                     //$('.ajax-loading').hide();
                     $('.loading').hide();
                 }
             });
             $('.loading').hide();
         });

         $('#addRange').on('submit', 'form', function (e) {
         e.preventDefault();
         $('.loading').show();
         var action = $(this).attr('action');
         $('.errorDiv').html('');
         $('.successMessage').html('');
         $('.errorMessage').html('');
         var _token = $("input[name='_token']").val();
         var from = $("input[name='from']").val();
         var to = $("input[name='to']").val();
         // var profit = $("input[name='profit']").val();
         var profit = $("input[name='profit']:checked").val();
         var amount = $("input[name='amount']").val();
         var sales_type_id = $("input[name='sales_type_id']").val();
            $.ajax({
                url: action,
                type:'POST',
                data: {_token:_token, from:from, to:to, profit:profit, amount:amount, sales_type_id:sales_type_id},
                success: function(response) {
                     if(response.success == true) {
                          console.log(response.msg);
                          $('.successMessage').html(response.msg+' It will be redirect with in 2 sec');
                          setTimeout(function(){
                               $('.loading').hide();
                               location.reload();
                          }, 2000);
                     } else {
                          console.log(response.msg);
                          $('.errorMessage').html(response.msg);
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
        $('#data-table').on('click', '.destroy', function (e) {
            e.preventDefault();
            $('.loading').show();
            var href = $(this).attr('href');

            bootbox.confirm("Do you want to delete this one", function (result) {
                if (result === false)
                    return;

                $.ajax({
                    url: href,
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    success: function () {
                         table.ajax.reload();
                         $('.loading').hide();
                         growl("You dont have sufficient privlilege to access this area.", "danger");
                    }
                });
            });
        });
        $(document).on('click', '.proft-type', function (e) {
             if($('#fixed').is(':checked')) {
                   $('#amount').attr('placeholder', 'Amount in USD');
                   $('.amount-label').html('Enter Amount');
              }  else if($('#percentage').is(':checked')) {
                   $('#amount').attr('placeholder', 'Percentage');
                   $('.amount-label').html('Enter Percentage Value');
             }
        });

    });

</script>
@endpush
