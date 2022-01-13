@include('trader.includes.status-msg')
<header class="header">
    <div class="container">
        <nav id="main-nav" class="navigation">
            <a class="navigation__logo" href="{{url(session()->get('language').'/')}}"><img class="navigation__logo" src="{{url('css/frontend/assets/img/logo.svg')}}" alt="watch my bid logo"></a>
            <ul class="navigation__list">
                <li class="navigation__item @if(Request::is(session()->get('language').'/home')) {{"active"}} @endif"><a href="{{url(session()->get('language').'/home')}}">{!! trans('frontend.home_menu') !!}</a></li>
                <li class="navigation__item @if(Request::is(session()->get('language').'/about')) {{"active"}} @endif"><a href="{{url(session()->get('language').'/about')}}">{!! trans('frontend.about_menu') !!} </a></li>
                <li class="navigation__item @if(Request::is(session()->get('language').'/contact')) {{"active"}} @endif"><a href="{{url(session()->get('language').'/contact')}}">{!! trans('frontend.contact_menu') !!}</a></li>
            </ul>
            <div class="user-control">
                <div class="user-control__log-in">
                    <i class="fa fa-user" aria-hidden="true"></i>
                    <span class="user-name">{{Auth::Guard('trader')->user()->first_name}}</span>
                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                    <div class="user-control-menu">
                        <ul>
                            <li><a href="{{url(session()->get('language').'/profile')}}">{!! trans('frontend.profile_menu') !!}</a></li>
                            <li><a href="{{url(session()->get('language').'/notifications')}}">{!! trans('frontend.notification_menu') !!}</a></li>
                            <li><a href="{{url(session()->get('language').'/history')}}">{!! trans('frontend.history_menu') !!}</a></li>
                            <li style="margin-left: auto;"><a href="{{url(session()->get('language').'/logout')}}" onclick=" return confirm('Are You sure you want to logout?');"> <small>{!! trans('frontend.logout_menu') !!} </small></a></li>
                        </ul>

                    </div>
                </div>
                @if(session()->get('language') == 'ar')
                <span class="lang"><a href="{{url('en/home')}}">English</a></span>
                @else
                {{-- <span class="lang"><a href="{{url('ar/home')}}">عربى</a></span> --}}
               @endif

            </div>
            


            <div class="mobile-menu">
                <button class="mobile-btn"><img src="{{url('css/frontend/assets/img/menu.svg')}}" alt="mobile menu"></button>
                <ul class="navigation__list">
                    <div class="user-mob-name">
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <span class="user-name">{{Auth::Guard('trader')->user()->first_name}}</span>
                    </div>
                    <li class="navigation__item"><a href="{{url(session()->get('language').'/')}}">{!! trans('frontend.home_menu') !!}</a></li>
                    <li class="navigation__item"><a href="{{url(session()->get('language').'/about')}}">{!! trans('frontend.about_menu') !!}</a></li>
                    <li class="navigation__item"><a href="{{url(session()->get('language').'/contact')}}">{!! trans('frontend.contact_menu') !!}</a></li>
                    <li class="navigation__item"><a href="{{url(session()->get('language').'/profile')}}">{!! trans('frontend.profile_menu') !!}</a></li>
                    <li class="navigation__item"><a href="{{url(session()->get('language').'/notifications')}}">{!! trans('frontend.notification_menu') !!}</a></li>
                    <li class="navigation__item"><a href="{{url(session()->get('language').'/history')}}"> {!! trans('frontend.history_menu') !!}</a></li>
                    <div class="log-out-mob"><a href="{{url(session()->get('language').'/logout')}}" onclick=" return confirm('Are You sure you want to logout?');">
                            <small>{!! trans('frontend.logout_menu') !!} </small></a>
                            @if(session()->get('language') == 'ar')
                            <span><a href="{{url('en/home')}}">English</a></span>
                            @else
                            {{-- <span><a href="{{url('ar/home')}}">عربى</a></span> --}}
                           @endif
                    </div>
                </ul>

            </div>
        </nav>
    </div>
</header>
<script>
var user_id = "{{Auth::Guard('trader')->user()->id}}";
var dealer_id = "{{Auth::Guard('trader')->user()->dealer_id}}";
</script>