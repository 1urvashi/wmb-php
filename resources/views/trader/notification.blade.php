@extends('trader.layouts.master',['title'=>trans('frontend.notify_title'),'class'=>'innerpage'])
@section('content')

<div class="container p-10">
    @if(count($notifications))
        @foreach($notifications as $notification)
            <div class="notification-item">
                <span class="notification-item__title">{{$notification->title}}</span>
                <p>{{$notification->desc}}</p>
                <time>{{ Carbon\Carbon::instance(DateTime::createFromFormat('Y-m-d H:i:s',$auction->UaeDate($auction->convertTimeToUTCzone($notification->created_at->format('Y-m-d H:i:s')))))->diffForHumans() }}</time>
            </div>
        @endforeach
    @endif
</div>


@endsection

@push('scripts')
  @endpush