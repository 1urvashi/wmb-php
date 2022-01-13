<head>
<meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@if($specialTitle) {{$title}} @else WatchMyBid - {{$title}} @endif</title>
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  {{--<meta name="_token" content="{!! csrf_token() !!}"/>--}}
  <!-- Bootstrap 3.3.6 -->

  {{-- @if(session()->get('language') == 'en')
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
<link rel="stylesheet" href="{{URL::asset('css/frontend/lightbox.css')}}"> --}}

<link rel="stylesheet" href="{{URL::asset('css/frontend/assets/css/main.min.css')}}">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/progressive-image.js/dist/progressive-image.css"> -->


 <script type="text/javascript">
 var timeNow = '<?php echo time()?>';
 var serverTimeNow = '<?php echo time(); ?>';
 systemTime = parseInt(Date.now()/1000);

 var timeDiffNow  = systemTime - serverTimeNow;

    <?php $auctionModel = new \App\Auction(); ?>
    var ongoingStatus = '{{ $auctionModel->getStatusType(1) }}';
  </script>

  </head>
