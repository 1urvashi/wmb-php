<!DOCTYPE html>
<html @if(session()->get('language') == 'ar') dir="rtl" lang="ar" @else lang="en"  @endif>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>WatchMyBid - Watch</title>
  <link rel="stylesheet" href="{{URL::asset('css/frontend/assets/css/main.min.css')}}" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
  <style>
    .error {
    color: red;
}
  </style>
</head>

<body>
  <div class="log-in-wrap">
  @include('trader.includes.status-msg')
      <div class="log-in-wrap__wrap">
        
      <div class="log-in-wrap__log-in">
        <div class="log-in-wrap__start">
          <img src="{{URL::asset('css/frontend/assets/img/logo.svg')}}" alt="wmb logo" />
        </div>
        <div class="log-in-wrap__end ">
          
          <span class="title">{{trans('frontend.login')}} </span>
          <p>{!! trans('frontend.enter_cred') !!}</p>
          <form action="{{ url(session()->get('language').'/login') }}" method="post" class="form-log-in form-validate" >
            {!! csrf_field() !!}
            <input type="email" name="email" id="q_email" required="" oninvalid="this.setCustomValidity('Please enter your email')" placeholder="{{trans('frontend.user_or_email')}}" oninput="setCustomValidity('')" aria-required="true" class="form-log-in__input" value="{{ old('email') }}" />

            <input type="password" class="form-log-in__input" required="" oninvalid="this.setCustomValidity('Please enter your password')" oninput="setCustomValidity('')"  name="password" id="q_pwd" placeholder="{{trans('frontend.password')}}"  aria-required="true" value="{{ old('password') }}"/>

            <div class="form-log-in__group">
                <input class="form-log-in__check" type="checkbox" name="terms" id="check" >
                <label class="form-log-in__label" for="check">{{trans('frontend.terms_label')}}</label>
            </div>

            
            <a href="{{url('trader/password/reset')}}">{{trans('frontend.forgot')}}</a>
                </br>
              </br>
                <div class="row">
                <div class="col-md-6">
                    <div class="g-recaptcha" required data-sitekey="{{env('CAPTCHA_CLIENT_KEY')}}"></div>
                    @if ($errors->has('g-recaptcha-response'))
                    <span class="help-block">
                        <strong>Captcha required</strong>
                    </span>
                    @endif
                    </div>
                </div>


            <div class="form-log-in__button-group">
              <button type="submit" class="btn btn-transparent signin-btn">{{trans('frontend.login')}}</button>

              <button type="button" id="signup"  class="btn btn-transparent regi-openBtn">{{trans('frontend.registration')}}</button>
              <!-- <a href="#" class="btn btn-transparent regi-openBtn">{{trans('frontend.sign_up')}}</a> -->
            </div>
          </form>
        </div>
        @if(isset($terms))
        <div id="my_popup" style="display: none">
             <span class="my_popup_close">X</span>
             <div class="data">
                  <h4>{{$terms->title}}</h4>
                  {!! $terms->body !!}
             </div>
         </div>
        @endif
        
      </div>
    </div>
  </div>
  {{-- <script src="{{URL::asset('css/frontend/assets/js/main.min.js')}}"></script> --}}

  <script src="{{URL::asset('js/jquery-3.1.1.min.js')}}"></script>
  <script src="{{URL::asset('js/bootstrap.min.js')}}"></script>
  <script src="{{URL::asset('js/jquery.validate.min.js')}}"></script>

  <script src="https://www.google.com/recaptcha/api.js?hl=en" async defer></script>
  <script src="https://cdn.rawgit.com/vast-engineering/jquery-popup-overlay/1.7.13/jquery.popupoverlay.js"></script>

  <script>
       $('document').ready(function() {
            $('#signup').click(function() {
            window.location = "{{url(session()->get('language').'/register')}}";
            });
            //$('.form-validate').validate();
            $('#my_popup').popup();
            // $('.form-validate').validate({
            //          rules: {
            //              email: {
            //                  required: true,
            //              },
            //              password: {
            //                  required: true,
            //              },
            //          },
            //      // Specify the validation error messages
            //          messages: {
            //              email: {
            //                  required: "{{ trans('frontend.error_email_req') }}",
            //              },
            //              password: {
            //                  required: "{{ trans('frontend.error_password') }}",
            //              },
            //          }
            //      });
        });         
    setTimeout(function() {
        $(".alert").slideUp()
    }, 3000);
</script>
</body>

</html>
