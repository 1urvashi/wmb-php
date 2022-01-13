@extends('dealer.layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Negotiate Bid Amount (For 10 mins)</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="{{url('dealer/auctions/owner_negototiate/post/'.$auction->id)}}" onSubmit="if(!confirm('Are you sure you want to override bid amount this  Auction?')){return false;}">
                     {{ csrf_field() }}
                     @if(isset($auction))<input name="_method" type="hidden" value="POST">@endif
                    <div class="box-body">
                        <div class="form-group">
                            <label>Bid Price</label>
                            <input type="text" class="form-control" id="bid_price" name="bid_price" value="{{$bid->price}}" onkeypress="return isFieldNumber(event)" disabled>
                            <input type="hidden"  name="current_bid_price" value="{{$bid->price}}">
                            <input type="hidden"  name="trader_id" value="{{$bid->trader_id}}">
                        </div>

                            <div class="form-group">
                            <label>Requested Amount</label>
                            <input type="text" class="form-control" id="override_bid_amount" name="override_bid_amount" value="{{old('override_bid_amount')}}" onkeypress="return isFieldNumber(event)">
                        </div>
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

           });


	function isFieldNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}


</script>
@endpush
