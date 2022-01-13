@if(!empty(Auth::guard('dealer')->user()->id))
     @php($actionAllowed =  1)
     @php($basePriceView =  1)
     @php($actionCancel = 1)
     @php($OngoingAuctionView =  1)
     @php($currentPriceView =  1)
@else
     @php($user = Auth::guard('admin')->user())
     @php($actionAllowed = !is_null($user) && $user && Gate::allows('auction-button_stop') ? 1 : 0)
     @php($actionCancel = !is_null($user) && $user && Gate::allows('auction-button_cancel') ? 1 : 0)
     @php($basePriceView = !is_null($user) && $user && Gate::allows('auction-column_base-price-read') ? 1 : 0)
     @php($OngoingAuctionView = !is_null($user) && $user && Gate::allows('auction-button_view') ? 1 : 0)
     @php($currentPriceView = !is_null($user) && $user && Gate::allows('auction-column_current-price-read') ? 1 : 0)
@endif

<table id="auction-table" class="table table-striped table-bordered table-hover dataTable no-footer" role="grid" aria-describedby="auction-table_info">
    <thead>
        <tr role="row">
            <th class="sorting_disabled" >Id</th>
            <th class="sorting_disabled">Title</th>
            <th class="sorting_disabled">Start Time</th>
            <th class="sorting_disabled">End Time</th>
            <th class="sorting_disabled">Minimum Increment</th>
            <th class="sorting_disabled">Type</th>
            <th class="sorting_disabled">Dealer Id</th>
            @if($basePriceView)
               <th class="sorting_disabled">Base Price</th>
            @endif
            @if($currentPriceView)
              <th class="sorting_disabled">Current Price</th>
            @endif
            <th class="sorting_disabled">Is Negotiated</th>
            <th class="sorting_disabled">Timer</th>

            <th class="sorting_disabled">Action</th>
        </tr>
    </thead>

    <tbody id="auction-list">

    </tbody>
</table>
@push('scripts')
<script>
@php($user = Auth::Guard('dealer')->user())

@if($user)
dealer_id = "{{$user->branch_id == 0 ? $user->id : $user->branch_id}}";
@else
dealer_id = '';
@endif
// var overTime = "00 <span>d</span> 00 <span>h</span> 00 <span>m</span> 00 <span>s</span>";
all = '<?php echo $all ?>';
function addBlocks(data){
    var actionAllowed = parseInt('{{$actionAllowed}}');
    var OngoingAuctionView = parseInt('{{$OngoingAuctionView}}');
    var actionCancel = parseInt('{{$actionCancel}}');
    var buyPriceView = parseInt('{{$actionAllowed}}');
    var basePriceView = parseInt('{{$basePriceView}}');
    var currentPriceView = parseInt('{{$currentPriceView}}');

    var dynamicAction = actionAllowed ?
    '<td class="ongoingAuctionAction"><a href="stop/'+data.id+'" onclick="return confirm('+"'Are you sure you want to stop this Auction?'"+');" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i> Stop</a>' : '';

    dynamicAction += actionCancel ? '<a href="cancel/'+data.id+'" onclick="return confirm('+"'Are you sure you want to cancel this Auction?'"+');" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Cancel</a>' : '';
    dynamicAction += OngoingAuctionView ? '<a href="view/'+data.id+'" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> View</a></td>' : '';

    data.price = data.hasOwnProperty('bidding_price') ? data.bidding_price : data.base_price;
    currentPrice = data.price ? '<td class="price">'+data.currency+' '+price(data.price)+'</td>' : '';
    currentPrice = currentPriceView ? currentPrice : '';

    basePrice = basePriceView ? '<td class="bprice">'+data.currency+' '+price(data.base_price)+'</td>' : '';

    var text = '<tr role="row" class="auction-'+data.id+'">';
               text += '<td>'+data.id+'</td>'+
                    '<td>'+data.title+'</td>'+
                    '<td>'+convertTimestamp(data.start_time)+'</td>'+
                    '<td>'+convertTimestamp(data.end_time)+'</td>'+
                    '<td>'+data.min_increment+'</td>'+
                    '<td>'+classType[data.type]+'</td>'+
                    '<td>'+data.dealer_id+'</td>'+
                    basePrice+
                    currentPrice+
                    '<td class="negotiated">'+(data.is_negotiated ? 'Yes' : 'No')+'</td>'+
                    '<td class="time"></td>'+
                      dynamicAction
                    +
                '</tr>';
    if(all == 0){
        if(data.dealer_id == dealer_id){
            $('#auction-list').append(text);
        }
    }else{
        $('#auction-list').append(text);
    }
}
function childAdded(childData){
    if(childData.status == 1 ){
      addBlocks(childData);
      $('.auction-'+childData.id).countdown(convertTimestamp(childData.end_time), function(event) {
        $(this).find('.time').html(event.strftime('<span class="days">%D</span> <span>d</span> <span class="hours">%H</span> <span>h</span> <span class="minutes">%M</span> <span>m</span> <span class="seconds">%S</span> <span>s</span>'));
          if(isExpired($(this).find('.time'))){
            $(this).hide();
          }
          }).on('finish.countdown', function(){
            if(isExpired($(this).find('.time'))){
              $(this).hide();
            }
          });
    }
}
function childChanged(childData){
  if(childData.status == 1 ){
      modifyBlock(childData);
      $('.auction-'+childData.id).countdown(convertTimestamp(childData.end_time), function(event) {
        $(this).find('.time').html(event.strftime('<span class="days">%D</span> <span>d</span> <span class="hours">%H</span> <span>h</span> <span class="minutes">%M</span> <span>m</span> <span class="seconds">%S</span> <span>s</span>'));
          if(isExpired($(this).find('.time'))){
            $(this).hide();
          }
      }).on('finish.countdown', function(){
          $(this).hide();
          if(isExpired($(this).find('.time'))){
            $(this).hide();
          }
     });
     $('.auction-'+childData.id+' .negotiated').html((childData.is_negotiated ? 'Yes' : 'No'));
  }else{
    $('.auction-'+childData.id).remove();
  }
}

function isExpired(element){
    days = $(element).find('.days').text();
    minutes = $(element).find('.minutes').text();
    hours = $(element).find('.hours').text();
    sec = $(element).find('.seconds').text();
    if(days == '00' && minutes == '00' && hours == '00' && sec == '00'){
        return true;
    }
    return false;
}
</script>

@endpush
