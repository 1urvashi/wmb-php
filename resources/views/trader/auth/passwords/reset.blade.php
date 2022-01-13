<!DOCTYPE html>
<html lang="en" dir="auto">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title></title>
  <link rel="stylesheet" href="{{URL::asset('css/frontend/assets/css/main.min.css')}}" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>

<body>
  <div class="log-in-wrap">
      @include('trader.includes.status-msg')
    <div class="log-in-wrap__wrap">
      <div class="log-in-wrap__log-in">
        <div class="log-in-wrap__start">
          <img src="{{URL::asset('css/frontend/assets/img/logo.svg')}}" alt="wmb logo" />
        </div>
        <div class="log-in-wrap__end log-in">
          <span class="title">{{trans('frontend.reset_txt')}}</span>
       
          <form action="{{ url('trader/password/reset') }}" method="post" class="form-log-in">
            {!! csrf_field() !!}
            <input type="hidden" name="token" value="{{ $token }}">

            <input id="q_email" type="email" class="form-log-in__input" required  name="email"  value="{{ $email or old('email') }}">
            
            <input id="password" type="password" class="form-log-in__input" required placeholder="New Password" name="password" value="{{ old('email') }}">

            <input id="password-confirm" type="password" class="form-log-in__input" required placeholder="Confirm Password" name="password_confirmation" value="{{ old('email') }}">


            <div class="form-log-in__button-group">
                <button type="submit" class="btn btn-transparent signin-btn">{{trans('frontend.reset')}}</button>
             
            </div>
          </form>
        </div>
    
        
      </div>
    </div>
  </div>

  <script src="{{URL::asset('css/frontend/assets/js/main.min.js')}}"></script>
  <script src="https://www.google.com/recaptcha/api.js?hl=en" async defer></script>
  <script src="https://cdn.rawgit.com/vast-engineering/jquery-popup-overlay/1.7.13/jquery.popupoverlay.js"></script>

  <script>
       $('document').ready(function() {
            $('#signup').click(function() {
            window.location = "{{url(session()->get('language').'/register')}}";
            });
            //$('.form-validate').validate();
            $('#my_popup').popup();
        });         
    setTimeout(function() {
        $(".alert").slideUp()
    }, 3000);
</script>
</body>

</html>
