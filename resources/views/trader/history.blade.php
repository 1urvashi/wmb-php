@extends('trader.layouts.master',['title'=>trans('frontend.history'),'class'=>'innerpage'])
@section('content')
<section class="section-history p-10">
    <div class="container">
        <div class="section-history__list">
           @if(count($auctions))
            @foreach($auctions as $auction)
            <div class="section-history__item">
                <label class="label {{strtolower($auction->getAuctionType($auction->type))}}">{{$auction->getAuctionType($auction->type)}}</label>

                <label class="label" style="background: {{$auction->getStatusColor($auction->getStatusValue($auction->status))}}">{{$auction->status}}</label>

                <div class="section-history__item-middle">
                    <figure>
                        <img src="{{$auction->objectImage() ? $auction->objectImage()->image : ''}}" alt="rolex">
                    </figure>
                    <div class="section-history__item-middle__end">
                        <h4>{{$auction->title}}</h4>
                      
                        <span><small>{{$auction->currency}}</small>  {{$auction->maxValue ? number_format($auction->maxValue->price) : ''}}</span>
                        <a class="btn btn--primary" href="{{url(session()->get('language').'/auction/detail/'.$auction->id.'?type=detail')}}">{{trans("frontend.details")}}</a>
                    </div>
                </div>
            </div>
            @endforeach
           @endif

        </div>
    </div>
</section>

@endsection

@push('scripts')
@endpush