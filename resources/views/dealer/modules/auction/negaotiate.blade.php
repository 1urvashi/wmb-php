@extends('dealer.layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Negotiate Auction for - <b>{{$object->name}} ({{$object->code}})</b></h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('dealer.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="{{url('dealer/auctions/negotiate/'.$auction->id)}}" >
                    {{ csrf_field() }}
                    <div class="box-body">
                        @if(!isset($auction)) <input type="hidden" class="form-control" name="object_id" value="{{$object->id}}"> @endif
                        <div class="form-group">
                            <label>Auction Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                            value="@if(isset($auction)){{$auction->title}}@else{{old('title')}}@endif" disabled>
                        </div>


                        <div class="form-group" id="start_time">
                            <label>Start Time</label>
                            <div class="input-group date form_datetime col-md-12"  data-date-format="MM d, yyyy HH:ii p" data-link-field="dtp_input1">
                                  <input class="form-control" size="16" name="start_time" type="text" value="@if(isset($auction)){{$auction->start_time}}@else{{old('start_time')}}@endif" disabled>
                                  <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                  <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Bid Price</label>
                            <input type="text" class="form-control" id="bid_price" name="base_price" value="{{$bidPrice}}" onkeypress="return isFieldNumber(event)" disabled>
                        </div>

                            <div class="form-group">
                            <label>Negotiation Price</label>
                            <input type="text" class="form-control" id="negotiate_price" name="negotiate_price" value="{{old('negotiate_price')}}" onkeypress="return isFieldNumber(event)">
                        </div>
                        <div class="form-group">
                             <label for=""><b>For 5 Min</b></label>
                            {{--<div class="checkbox">
                                <label>
                                    <input id="formin" name="formin" type="checkbox" value="1">
                                    For 5 Min
                                </label>
                            </div> --}}
                        </div>
                        {{--<div class="form-group"  id="end_time_wrapper">
                            <label>End Time</label>
                            <div class="input-group date form_datetime col-md-12"  data-date-format="MM d, yyyy HH:ii p" data-link-field="dtp_input1">
                                  <!-- <input class="form-control" size="16" name="end_time" type="text" value="{{old('end_time')}}"> -->
                                  <input class="form-control" size="16" name="end_time" type="text" value="@if(isset($auction)){{$auction->end_time}}@else{{old('end_time')}}@endif">
                                  <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                  <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                            </div>
                       </div>--}}
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
		  $(function () {
                // $('#start_time').datetimepicker({  minDate:new Date()});
           });

    $('.form_datetime').datetimepicker({
	format: "dd MM yyyy HH:ii P",
        showMeridian: true,
        autoclose: true,
        todayBtn: true
    });
    $('#immediate').click(function(){
        //console.log(1);
        if($('#immediate').is(':checked')){
            $('#start_time').hide();
        }else{
            $('#start_time').show()
        }
    })


	var auctionType = $('#auctionType').val();
	checkAuction(auctionType);

	$('#auctionType').change(function (e) {
    	var auctionType = $(this, ':selected').val();

		checkAuction(auctionType);
	});


	function checkAuction(type){
		if(type == 5001 || type == 5002 ){
			 $('#buy_section').show()
		}else{
			$('#buy_section').hide()
		}
	}

     $('#formin').click(function(){
        if($('#formin').is(':checked')){
            $('#end_time_wrapper').hide();
        }else{
            $('#end_time_wrapper').show();
        }
   });

   $('form').submit(function(){
    $('form button').attr('disabled',true);
});



</script>
@endpush
