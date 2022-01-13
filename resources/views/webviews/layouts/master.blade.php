<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="_token" content="{!! csrf_token() !!}"/>
        <title>WatchMyBid</title>
        <link rel="icon" type="image/png" href="images/favicon.png"/>
        @if(session()->get('language') == 'en')
           <link rel="stylesheet" href="{{URL::asset('css/bootstrap.min.css')}}">
        @else
           <link rel="stylesheet" href="{{URL::asset('css/bootstrap-ar.min.css')}}">
        @endif

        <link rel="stylesheet" href="{{URL::asset('css/font-awesome.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('plugins/select2/select2.min.css')}}">


      <link rel="stylesheet" href="{{URL::asset('css/frontend/normalize.css')}}">
      <link rel="stylesheet" href="{{URL::asset('css/frontend/nouislider.css')}}">
      <link rel="stylesheet" href="{{URL::asset('css/frontend/flexslider.css')}}">
      <link rel="stylesheet" href="{{URL::asset('css/frontend/select2.css')}}">

        @if(session()->get('language') == 'en')
          <link rel="stylesheet" href="{{URL::asset('css/frontend/style.css')}}">
        @else
          <link rel="stylesheet" href="{{URL::asset('css/frontend/style-ar.css')}}">
        @endif

      <link rel="stylesheet" href="{{URL::asset('css/frontend/misc.css')}}">

      @if(session()->get('language') == 'en')
        <link rel="stylesheet" href="{{URL::asset('css/frontend/grid.css')}}">
      @else
        <link rel="stylesheet" href="{{URL::asset('css/frontend/grid-ar.css')}}">
      @endif
      <link rel="stylesheet" href="{{URL::asset('css/frontend/lightbox.css')}}">
        <style>
			body{
				padding: 15px;
			}
            body.araibc{
                font-family: 'Cairo', sans-serif;
				direction: rtl;
            }
                h1,h2{font-weight: 700;font-size: 24px; text-align: center;}
                p{font-weight: 300;margin-bottom: 20px;font-size: 16px;line-height: 160%;}
                ul,ol{padding-right: 35px;font-size: 16px;}
                ul li, ol li{padding-bottom: 10px;line-height: 160%;}
        </style>
        @yield('styles')
    </head>

    <body style="background:none" class="webview  @if(session()->get('language') == 'ar') araibc @endif">
        @yield('content')

    </body>
</html>
