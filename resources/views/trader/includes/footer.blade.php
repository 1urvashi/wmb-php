<footer class="footer p-10">
    <div class="container">
        <div class="footer__wrap">
            <div class="footer__item">
                <h5>{!! trans('frontend.company_menu') !!}</h5>
                <ul>
                    <li class="@if(Request::is(session()->get('language').'/about')) {{"active"}} @endif"><a href="{{url(session()->get('language').'/about')}}">{!! trans('frontend.about_menu') !!}</a></li>
                    <li class="@if(Request::is(session()->get('language').'/privacy_policy')) {{"active"}} @endif"><a href="{{url(session()->get('language').'/privacy_policy')}}">{!! trans('frontend.privacy_menu') !!}</a></li>
                    <li class="@if(Request::is(session()->get('language').'/terms_service')) {{"active"}} @endif"><a href="{{url(session()->get('language').'/terms_service')}}">{!! trans('frontend.terms_menu') !!}</a></li>
                </ul>
            </div>
            <div class="footer__item">
                <h5>{!! trans('frontend.support_menu') !!}</h5>
                <ul>
                    <li class="@if(Request::is(session()->get('language').'/faq')) {{"active"}} @endif"><a href="{{url(session()->get('language').'/faq')}}">{!! trans('frontend.faq_menu') !!}</a></li>
                    <li class="@if(Request::is(session()->get('language').'/contact')) {{"active"}} @endif"><a href="{{url(session()->get('language').'/contact')}}">{!! trans('frontend.contact_menu') !!}</a></li>
                </ul>
            </div>
            <div class="footer__item">
                <h5>{!! trans('frontend.get_started') !!}</h5>

                <p>{!! trans('frontend.get_started_desc') !!}</p>
                <a class="btn btn-transparent" href="{{url(session()->get('language').'/contact')}}">{!! trans('frontend.contact_menu') !!}</a>
                <ul class="social-link">
                    <li class="social-link__item"><a href="https://www.facebook.com/watchmybid"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>
                    {{-- <li class="social-link__item"><a href="http://"><i class="fa fa-twitter-square" aria-hidden="true"></i></i></a></li> --}}
                    <li class="social-link__item"><a href="https://www.instagram.com/watchmybid/"><i class="fa fa-instagram" aria-hidden="true"></i></i></a></li>
                    <li class="social-link__item"><a href="https://www.youtube.com/channel/UCYIc5JnrTAMQn82lB88rGUA"><i class="fa fa-youtube-square" aria-hidden="true"></i></i></a></li>
                </ul>
            </div>
        </div>
    </div>
    <p class="copy" style="margin-bottom: 0;"><small>© 2021 All rights reserved. WATCHMYBID </small></p>
</footer>
{{-- <script src="{{URL::asset('js/jquery-3.1.1.min.js')}}"></script> --}}


<script src="{{URL::asset('css/frontend/assets/js/main.min.js')}}"></script>

{{-- <script src="{{URL::asset('js/bootstrap.min.js')}}"></script> --}}

<!-- datepicker -->
{{-- <script src="{{URL::asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script> --}}

<script src="{{URL::asset('js/jquery.countdown.min.js')}}"></script>
{{-- <script src="{{URL::asset('plugins/datetimepicker/jquery.datetimepicker.js')}}"></script> --}}
{{-- <script type="text/javascript" src="{{URL::asset('plugins/datepicker/js/bootstrap-datetimepicker.js')}}" charset="UTF-8"></script> --}}

{{-- <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script> --}}
{{-- <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script> --}}
  <script src="https://www.gstatic.com/firebasejs/3.2.0/firebase.js"></script>
  <script>



    //   $(document).ready(function(){
    //         $("#caranim").get(0).pause();
    //         $('#body-work .flexslider').viewportChecker({
    //             classToAdd: 'visible',
    //             callbackFunction: function(elem, action){
    //                 $("#caranim").get(0).play();
    //                 setTimeout(function () {
    //                     $(".video_wrapper").fadeOut();
    //                 }, 10000);
    //                 setTimeout(function () {
    //                     $("#body-work ul.slides").removeClass('hidden');
    //                 }, 10000);
    //             }
    //         });
    //     });


    //   jQuery(".js-example-basic-multiple").select2({
    //      placeholder: "Select make"
    //   });


  </script>

  <script>
      var firebaseTime = '';
    setTimeout(function() {
        $(".alert").slideUp()
     }, 3000);

     var config = {
         apiKey: '{{ env('FIREBASE_API_KEY') }}',
         authDomain: '{{ env('FIREBASE_AUTH_DOMAIN') }}',
         databaseURL:'{{ env('FIREBASE_DATABASE_URL') }}',
         projectId:'{{ env('FIREBASE_PROJECT_ID') }}',
         storageBucket:'{{ env('FIREBASE_STORAGE_BUCKET') }}',
         messagingSenderId:'{{ env('FIREBASE_MESSAGE_SENDER_ID') }}'
     };

    firebase.initializeApp(config);
    var database = firebase.database();
    var timeRef = database.ref('time');
    timeRef.set(firebase.database.ServerValue.TIMESTAMP);
    firebase.database().ref("time").on('value', function(offset) {
	timeDifference = (new Date().getTime()) - offset.val();
    });
    /*firebase.auth().onAuthStateChanged(function (user) {
        if (user) {
            var ref = new Firebase("https://WatchMyBid-1c878.firebaseio.com");
            ref.on("value", function (snapshot) {

            }, function (errorObject) {
                console.log("The read failed: " + errorObject.code);
            });
        } else {
            firebase.auth().signInAnonymously();
        }
    });*/
  </script>
  <script src="{{URL::asset('js/common.js?ver=1.072')}}"></script>
<script>
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});

setTimeout(function(){ 
            $('.alert-success,.alert-danger').hide();
        }, 3000);
// jQuery('.flexslider').flexslider({
// 	    animation: "slide"
// 	 });

</script>
