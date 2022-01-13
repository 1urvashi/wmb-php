@extends('trader.layouts.master',['title'=>'detail','class'=>'innerpage','type'=>$auction->getAuctionType($auction->type)])
@section('content')
<section id="details">
            <div class="container">
                 <div class="content row">
                        <div class="thumb col-md-6">
                            <div class="flexslider">
                                <ul class="slides">
                                    @foreach($auction->images as $_image)
                                    <li><img src="{{$_image->image}}" alt=""></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>    
                        <div class="col-md-6" id="auction-detail">
                            <div class="details row" id="auction-detail-46">
                               <h3>{{$auction->title}}</h3>
                               <div class="col-xs-6 right">
                                  <h4>{{trans('frontend.bid_amount')}}</h4>
                                  <p class="detail">  <span>{{$auction->currency}} </span>{{$bidAmount}}</p>
                               </div>
                            </div>
                        </div>
                 </div>
                    @include('trader.includes.specs',['attributeSet'=>$attributeSet])

                    <div class="col-md-12">
                        <h4 style="font-weight: bold;">Documentation (attachment)</h4>
                            @foreach($object->attachments as $attachment)
                            {{-- @php($pdfattachment= pathinfo($attachment->attachment, PATHINFO_EXTENSION))
                            @if($pdfattachment == 'pdf')
                            <a href="{{$attachment->attachment}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" width="100%"></a>
                            @else --}}
    
                            <img style="max-height: 200px;" src="{{$attachment->attachment}}"
                            data-darkbox="{{$attachment->attachment}}">
    
                                        {{-- @endif --}}
    
    
                            @endforeach
                    </div>
        </section>
@endsection

@push('scripts')
<script>
</script>
  @endpush