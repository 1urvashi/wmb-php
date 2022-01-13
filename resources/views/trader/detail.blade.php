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

            <div class="item-detail-info__info">
                <span class="label label--sold">{{$auction->status}}</span>
                <h2 class="item-detail-info__title mt-3">{{$auction->title}}</h2>
                <span class="item-detail-info__sub-title">{{trans('frontend.bid_amount')}}</span>
                <span class="item-detail-info__amount"><small>{{$auction->currency}}</small> {{$bidAmount}}</span>
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
