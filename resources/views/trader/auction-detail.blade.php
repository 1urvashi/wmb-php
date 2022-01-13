@extends('trader.layouts.master',['title'=>'detail','class'=>'innerpage','type'=>$auction->getAuctionType($auction->type)])
@section('content')
<section class="section-item-detail p-10">
    <div class="container">

        <div class="item-detail-info">
            <ul class="one-slider">
                @php($images = $auction->images ? $auction->images : [])
                 @if(count($images))
                    @foreach($images  as $_image)
                        <li class="item-detail-info__slider">
                            <figure data-href="{{$_image->image}}" class="progressive replace">
                                <img class="preview" src="{{url("css/frontend/assets/img/demmy-small.jpg")}}" alt="watch">
                            </figure>
                        </li>
                    @endforeach
                  @endif
            </ul>
            <div class="item-detail-info__info auction-detail" id="auction-detail-{{$auction->id}}">

                <span class="negotiate-text" style="display: none">{{trans("frontend.negotiate_bid")}}</span>
                <span class="own-text" style="display: none">{{trans("frontend.own_bid")}}</span>

                <h2 class="item-detail-info__title">{{$auction->title}}</h2>

                <div class="deal-item__middle">
                    <div class="deal-item__middle__start">
                        <span class="sub-title">{{trans("frontend.time_left")}}</span>
                        <time class="detail time"></time>
                    </div>
                    <div class="deal-item__middle__end">
                        <span class="sub-title">{{trans("frontend.cur_bid")}}</span>
                        <span>{{$auction->currency}}</span> <span class="amount current-bid"></span></span>
                    </div>
                </div>
                {{-- <p class="item-detail-info__vat">{{trans("frontend.vat_txt")}}</p> --}}

                <p class="next">{{trans("frontend.bid_next")}} : <span>{{$auction->currency}} </span><span class="next-bid"></span></p>

                {{-- <span class="item-detail-info__bid-next">{{trans("frontend.bid_next")}} : <small>{{$auction->currency}}</small> 21,000</span> --}}

                <button  id="next-bid" class="btn btn--primary next_button">{{trans("frontend.bid_next")}}</button>
                <span class="or">{{trans("frontend.or")}}</span>

                <form action="" class="form-bid bid-now" id="bid-now">
                    <div class="form-bid__group">
                        <input type="number" class="form-bid__input" id="bid_now" max="999999999999" placeholder="{{trans('frontend.your_bid')}}">
                        <button class="btn btn--primary">{{trans("frontend.bid_now")}}</button>
                    </div>
                </form>

                    <p id="max-amount">
                        @if($automaticBid)
                        {{trans("frontend.max_amount")}} : <span class="amt-currency">{{$auction->currency}} </span><span class="amt-value">{{number_format((int)$automaticBid->amount)}}</span>
                        @endif
                    </p>
                    <form action="" class="bid-now" id="bid-max">
                        <div class="form-bid__group">
                            <input type="number" class="form-bid__input" max="999999999999" id="bid_max" placeholder="{{trans('frontend.max_bid')}}">
                            <button class="btn btn--primary">{{trans("frontend.add")}}</button>
                        </div>
                    </form>
            </div>
        </div>

        @include('trader.includes.specs',['attributeSet'=>$attributeSet])
        
        <div class="documentation">
            <h3 class="title">Documentation(attachment)</h3>
            <ul class="documentation__list">
                @php($attachments = $object->attachments ? $object->attachments : [])
                @if(count($attachments))
                    @foreach($attachments  as $_attachment)
                    <li class="documentation__item">
                        <a href="{{$_attachment->attachment}}" data-toggle="lightbox" data-gallery="gallery">
                            <figure data-href="{{$_attachment->attachment}}" class="progressive replace">
                                <img class="preview" src="{{url('css/frontend/assets/img/demmy-small.jpg')}}}" alt="watch">
                            </figure>
                        </a>
                    </li>
                    @endforeach
                @endif

            </ul>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
var auctionId = '{!! $auction->id !!}';
session = "{!! url(session()->get('language')) !!}";
var auctions = database.ref('auctions/'+auctionId);
database.ref().child('auctions/'+auctionId).once('value').then(function(snapshot) {
    var childData = snapshot.val();
    detailTemplate(childData);
    $('.auction-detail .time').countdown(convertTimestamp(childData.end_time), function(event) {
        $(this).html(event.strftime('<span id="day">%D</span>d<span id="hour">%H</span>h<span id="minutes">%M</span>m<span id="sec">%S</span>s'));
    }).on('finish.countdown', function(){
        if(isExpired(this)){
            window.location.href = session;
        }
    });
});
function childChanged(childData){
    detailTemplate(childData);
    $('.auction-detail .time').countdown(convertTimestamp(childData.end_time), function(event) {
        $(this).html(event.strftime('<span id="day">%D</span>d<span id="hour">%H</span>h<span id="minutes">%M</span>m<span id="sec">%S</span>s'));
    }).on('finish.countdown', function(){
        if(isExpired(this)){
            window.location.href = session;
        }
    });
}
function detailTemplate(data){
    data.price = data.hasOwnProperty('bidding_price') ? data.bidding_price : data.base_price;
    buynow = ((data.buy_price >= data.price) && (type[data.type] != 'Live')) ? '<p class="buy-now-price">{{trans("frontend.buy_price")}}  <span>{{$auction->currency}} </span><span class="current-bid">'+data.buy_price+'</span></p><button id="buy-now" class="next_button">{{trans("frontend.buy_now")}}</button>' : '';
    if(data.final_req_amount > 0){
      $("#auction-detail-"+data.id).find('.current-bid').html(price(data.price));
      $(".next").hide();
      $(".bid-now, .or, #next-bid").hide();

       $("#auction-detail-"+data.id).find('#accept-bid').html('{{trans("frontend.accept_for")}} : <span class="amt-currency">{{$auction->currency}} </span><span class="amt-value">'+data.final_req_amount+'</span>').show();


    }else{
        $("#auction-detail-"+data.id).find('.current-bid').html(price(data.price));
        $("#auction-detail-"+data.id).find('.next-bid').html(price(data.price+data.min_increment));
        $("#auction-detail-"+data.id).find('#next-bid').attr('value',data.price+data.min_increment);
        $("#auction-detail-"+data.id).find('#buy-now-text').html(buynow).show();
        if(data.is_negotiated == 1){
            $("#auction-detail-"+data.id).find('#negotiate').html('{{trans("frontend.neg_amount")}} : <span class="amt-currency">{{$auction->currency}} </span><span class="amt-value">'+data.negotiated_amount+'</span>');
            $("#auction-detail-"+data.id).find('.negotiate-text').show();
        }else{
            $("#auction-detail-"+data.id).find('#negotiate').empty();
            $("#auction-detail-"+data.id).find('.negotiate-text').hide();
        }
        if(data.bid_trader_id == user_id){
            $("#auction-detail-"+data.id).find('.own-text').show();
        }else{
            $("#auction-detail-"+data.id).find('.own-text').hide();
        }
     }
}

jQuery('body').on('click', '#next-bid', function (e) {
    e.preventDefault();
    bid(this,$(this).attr('value') );
});



jQuery('body').on('click', '#accept-bid', function (e) {
  e.preventDefault();
  var lang = "{!! session()->get('language') !!}";
  var sessionId = "{{Auth::guard('trader')->user()->session_id}}";
  var params = jQuery.extend({}, doAjax_params_default);
  params['requestType'] = 'POST';
  params['data'] = {"auctionId":"{{ $auction->id }}","language":lang,"api_token":"{{ Auth::guard('trader')->user()->api_token }}", "session_id": sessionId};
  params['url'] = "{{ url('api/trader/settleNow') }}";

  var result = confirm("{{trans('frontend.owner_nego_alert')}}");
  if (result) {
      params['successCallbackFunction'] = function(response){
          if(response.StatusCode == '20000'){
              $('.ajax-error').html(response.Status).show();
              window.location.href = session;
          }else if(response.StatusCode == '30000'){
               window.location.replace("{{url(session()->get('language').'/logout')}}");
          }else{
              $('.ajax-success').html(response.Status).show();
          }
      }
      doAjax(params);
  }else {
      return false;
  }
});

jQuery('body').on('click', '#buy-now', function (e) {
    e.preventDefault();
    var lang = "{!! session()->get('language') !!}";
    var sessionId = "{{Auth::guard('trader')->user()->session_id}}";
    var params = jQuery.extend({}, doAjax_params_default);
    params['requestType'] = 'POST';
    params['data'] = {"auctionId":"{{ $auction->id }}","language":lang,"api_token":"{{ Auth::guard('trader')->user()->api_token }}", "session_id": sessionId};
    params['url'] = "{{ url('api/trader/buyBidNow') }}";
    params['successCallbackFunction'] = function(response){
        if(response.StatusCode == '20000'){
            $('.ajax-error').html(response.Status).show();
            window.location.href = session;
        }else if(response.StatusCode == '30000'){
             window.location.replace("{{url(session()->get('language').'/logout')}}");
        }else{
            $('.ajax-success').html(response.Status).show();
        }
    }
    doAjax(params);
});
jQuery('body').on('submit', '#bid-now', function (e) {
    e.preventDefault();
    if($('#bid_now').val()){
        bid(this, $('#bid_now').val());
    }else{
        $('.ajax-error').html("{{trans('frontend.enter_data')}}").show();
        setTimeout(function() { $('.alert').slideUp() }, 3000);
    }
    this.reset();
});
jQuery('body').on('submit', '#bid-max', function (e) {
    e.preventDefault();
    if($('#bid_max').val()){
        bid(this, $('#bid_max').val(),true);
    }else{
        $('.ajax-error').html("{{trans('frontend.enter_data')}}").show();
        setTimeout(function() { $('.alert').slideUp() }, 3000);
    }
    this.reset();
});
function bid(element, price,isAmount){
    isAmount = (typeof isAmount === 'undefined') ? false : isAmount;
    var lang = "{!! session()->get('language') !!}";
    var sessionId = "{{Auth::guard('trader')->user()->session_id}}";
    var params = jQuery.extend({}, doAjax_params_default);
    params['requestType'] = 'POST';
    data = {"auctionId":"{{ $auction->id }}","language":lang,"api_token":"{{ Auth::guard('trader')->user()->api_token }}", "session_id": sessionId};
    if(isAmount){
        data["amount"] = price;
        urlValue = 'setAutomaticBid';
    }else{
        data["price"] = price;
        urlValue = 'addBid';
    }
    params['data'] = data;
    params['url'] = "{{ url('api/trader/') }}/"+urlValue;
    params['successCallbackFunction'] = function(response){
        if(response.StatusCode == '20000'){
            $('.ajax-error').html(response.Status).show();
       }else if(response.StatusCode == '30000'){
            window.location.replace("{{url(session()->get('language').'/logout')}}");
       }else{
            if(urlValue == 'setAutomaticBid'){
                $('#max-amount').html('{{trans("frontend.max_amount")}} : <span class="amt-currency">{{$auction->currency}} </span><span class="amt-value">'+response.Data.amount+'</span>');
            }
            $('.ajax-success').html(response.Status).show();
        }
    }
    doAjax(params);
}
function removeBlock(data){
    window.location.href = session;
}
</script>
  @endpush
