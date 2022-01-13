@extends('trader.layouts.master',['title'=>trans('frontend.home_title'),'class'=>'homepage'])
@section('content')

<section class="section-deals p-10">
    <div class="container">
        <div class="seach-bar form-search">
            <input type="text" id="auction_search" onkeyup="search()" class="form-search__input" placeholder="{!! trans('frontend.auction_search') !!}">
            <label for="search" class="form-search__label">{!! trans('frontend.auction_search') !!}</label>
       </div>

        <nav class="item-nav-tab">
            <div class="nav" id="nav-tab" role="tablist">
                <a class="item-nav-tab__nav-link active" id="all" data-toggle="tab" href="#nav-all" role="tab" aria-controls="All" aria-selected="true" title="all">{!! trans('frontend.all') !!}</a>
                <a class="item-nav-tab__nav-link " id="live" data-toggle="tab" href="#nav-live" role="tab" aria-controls="Live" aria-selected="false" title="live">{!! trans('frontend.live') !!}</a>
                <a class="item-nav-tab__nav-link " id="corporate" data-toggle="tab" href="#nav-corporate" role="tab" aria-controls="corporate" aria-selected="false" title="corporate">{!! trans('frontend.corporate') !!}</a>
                <a class="item-nav-tab__nav-link" id="inventory" data-toggle="tab" href="#nav-inventory" role="tab" aria-controls="inventory" aria-selected="false" title="inventory">{!! trans('frontend.inven') !!}</a>

                <a class="item-nav-tab__nav-link" id="deals" data-toggle="tab" href="#nav-deals" role="tab" aria-controls="deals" aria-selected="false" title="deals">{!! trans('frontend.deals') !!}</a>

            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="all">
                <div class="deal-item-wrap">


                </div>
            </div>

            <div class="tab-pane fade" id="nav-live" role="tabpanel" aria-labelledby="live">
                <div class="deal-item-wrap">

                    {{-- <p class="nodatadiv" style="display: none">No Dta</p> --}}
                </div>
            </div>

            <div class="tab-pane fade" id="nav-corporate" role="tabpanel" aria-labelledby="corporate">
                <div class="deal-item-wrap">



                </div>
            </div>


            <div class="tab-pane fade" id="nav-inventory" role="tabpanel" aria-labelledby="inventory">
                <div class="deal-item-wrap">

                    {{-- <p class="nodatadiv" style="display: none">No Dta</p> --}}

                </div>
            </div>

            <div class="tab-pane fade" id="nav-deals" role="tabpanel" aria-labelledby="deals">
                <div class="deal-item-wrap">



                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>


dealer_id = "{{Auth::Guard('trader')->user() ? Auth::Guard('trader')->user()->dealer_id : ''}}";
detail_url = "{!! url(session()->get('language')) !!}";
function blockTemplate(data, negotiated){

    data.price = data.hasOwnProperty('bidding_price') ? data.bidding_price : data.base_price;
    url = detail_url+'/auction/detail/'+data.id
    ownAuction = (user_id == data.bid_trader_id) ? '<span class="own-text">{{trans("frontend.own_bid")}}</span>' : '<span style="display:none" class="own-text">{{trans("frontend.own_bid")}}</span>';
    if(data.final_req_amount > 0){
        negotiatedOwn = (negotiated == 1) ? '<span class="negotiate-text">{{trans("frontend.negotiate_owner_bid")}}</span>' : '<span style="display:none" class="negotiate-text">{{trans("frontend.negotiate_owner_bid")}}</span>';
    } else {
      negotiatedOwn = (negotiated == 1) ? '<span class="negotiate-text">{{trans("frontend.negotiate_bid")}}</span>' : '<span style="display:none" class="negotiate-text">{{trans("frontend.negotiate_bid")}}</span>';
    }
    var text = '<div class="auction-data deal-item module auction-'+data.id+'"">'+
                        '<div class="tags">'+
                            '<label class="'+classType[data.type]+'">'+type[data.type]+'</label>'+
                            negotiatedOwn+
                         ownAuction+
                        '</div>   '+
                        '<figure data-href="'+data.image+'" class="progressive replace">'+
                            '<img class="preview" src="{{url("css/frontend/assets/img/demmy-small.jpg")}}" alt="'+data.title+'">'+
                        '</figure>'+
                        '<h2 class="title">'+data.title+'</h2>'+
                        '<div class="deal-item__middle">'+
                            '<div class="deal-item__middle__start">'+
                                '<span class="sub-title">{{trans("frontend.time_left")}}</span>'+
                                '<time class="time"></time>'+
                            '</div>'+
                            '<div class="deal-item__middle__end">'+
                                '<span class="sub-title">{{trans("frontend.cur_bid")}}</span><p class="price">'+
                                '<span>'+data.currency+'</span> <span class="amount">'+price(data.price)+'</span></p>'+
                            '</div>'+
                        '</div>'+
                        '<a class="btn btn--primary" href="'+url+'">{{trans("frontend.details")}}</a>'+
                    '</div>';

        return text;


}

function search() {
    var input = document.getElementById("auction_search");
    var filter = input.value.toLowerCase();
    var nodes = document.getElementsByClassName('auction-data');

    for (i = 0; i < nodes.length; i++) {
      if (nodes[i].innerText.toLowerCase().includes(filter)) {
        nodes[i].style.display = "block";
      } else {
        nodes[i].style.display = "none";
      }
    }
  }




</script>
@endpush
