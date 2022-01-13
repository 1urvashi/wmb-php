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

                    <div class="documentation">
                        <h3 class="title">Documentation(attachment)</h3>
                        <ul class="documentation__list">
                            @php($attachments = $object->attachments ? $object->attachments : [])
                            @if(count($attachments))
                                @foreach($attachments  as $_attachment)
                                <li class="documentation__item">
                                @php($pdfattachment= pathinfo($attachment->attachment, PATHINFO_EXTENSION))
                                @if($pdfattachment == 'pdf')
                                <a href="{{$attachment->attachment}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" width="100%"></a>
                                @else
                                    <a href="{{$_attachment->attachment}}" data-toggle="lightbox" data-gallery="gallery">
                                        <figure data-href="{{$_attachment->attachment}}" class="progressive replace">
                                            <img class="preview" src="{{url('css/frontend/assets/img/demmy-small.jpg')}}}" alt="watch">
                                        </figure>
                                    </a>
                               @endif
            
                                </li>
                                @endforeach
                            @endif
            
                        </ul>
                    </div>
        </section>
@endsection

@push('scripts')
<script>
</script>
  @endpush