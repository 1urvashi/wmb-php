@extends('trader.layouts.master',['title'=>Auth::guard('trader')->user()->first_name,'class'=>'innerpage'])
@section('content')

<section id="profile">
            <div class="container">
                <div class="row content">
                    <div class="col-md-12 pro-pic">
                       <a href="{{Auth::guard('trader')->user()->image}}" data-lightbox="profile">
                         {{--  <img src="{{Auth::guard('trader')->user()->image}}" alt="">  --}}
                        </a>
                        <h3>{{Auth::guard('trader')->user()->first_name}} {{Auth::guard('trader')->user()->last_name}}</h3>

                        
                    </div>

                    <div class="col-md-3 col-sm-6 item">
                      <h4>{{trans('frontend.profile_first_name')}}</h4>
                      <p>{{Auth::guard('trader')->user()->first_name}}</p>
                  </div>
                  <div class="col-md-3 col-sm-6 item">
                    <h4>{{trans('frontend.profile_last_name')}}</h4>
                    <p>{{Auth::guard('trader')->user()->last_name}}</p>
                </div>
                    <div class="col-md-3 col-sm-6 item">
                        <h4>{{trans('frontend.profile_email')}}</h4>
                        <p>{{Auth::guard('trader')->user()->email}}</p>
                    </div>
                    {{--  <div class="col-md-3 col-sm-6 item">
                        <h4>{{trans('frontend.profile_mobile')}}</h4>
                        <p>{{Auth::guard('trader')->user()->mobile}}</p>
                    </div>  --}}

                      <div class="col-md-3 col-sm-6 item">
                        <h4>{{trans('frontend.profile_phone')}}</h4>
                        <p>{{Auth::guard('trader')->user()->phone}}</p>
                    </div>

                      {{--  <div class="col-md-3 col-sm-6 item">
                        <h4>{{trans('frontend.profile_rta')}}</h4>
                        <p>{{Auth::guard('trader')->user()->rta_file}}</p>
                    </div>  --}}

                      {{--  <div class="col-md-3 col-sm-6 item">
                        <h4>{{trans('frontend.profile_credit')}}</h4>
                        <p>{{Auth::guard('trader')->user()->credit_limit}}</p>
                    </div>

                      <div class="col-md-3 col-sm-6 item">
                        <h4>{{trans('frontend.profile_deposit')}}</h4>
                        <p>{{Auth::guard('trader')->user()->deposit_amount}}</p>
                    </div>  --}}
                </div>
                {{--  @php($ext_passport = pathinfo(Auth::guard('trader')->user()->passport, PATHINFO_EXTENSION))
                @php($ext_trade_license = pathinfo(Auth::guard('trader')->user()->trade_license, PATHINFO_EXTENSION))
                @php($ext_document = pathinfo(Auth::guard('trader')->user()->document, PATHINFO_EXTENSION))
                @php($ext_kyc = pathinfo(Auth::guard('trader')->user()->kyc, PATHINFO_EXTENSION))
                @php($ext_payment_receipt = pathinfo(Auth::guard('trader')->user()->payment_receipt, PATHINFO_EXTENSION))  --}}

                
               
                <div class="row content">
              
                  
                    <div class="col-md-4 det">
                      <h3>
                          {{trans('frontend.emirates_id_front')}}
                      </h3>
                      {{-- <label for="imageUpload" class="avatar-editt-profile" onclick='$("#imageUpload").click()' ><i
                        class="fa fa-pencil"></i>edit</label> --}}
                      @foreach($user->traderImages  as $emirates_id_front)
                       @php($eif = pathinfo($emirates_id_front->image, PATHINFO_EXTENSION))
                        @if(!empty($emirates_id_front))
                          @if($eif == 'pdf')
                          <a href="{{$emirates_id_front->image}}" target="_blank" style="color: white; font-weight: bold;">
                              <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                          </a>
                          @else
                            @if($emirates_id_front->imageType == 'emirates_id_front')
                            <a href="{{$emirates_id_front->image}}" rel="lightbox[IDF]" >
                                <img src="{{$emirates_id_front->image}}" alt="">
                              </a>
                              
                            @endif
                          @endif
                        @endif
                      @endforeach
                  </div>

                    <div class="col-md-4 det">
                      <h3>
                          {{trans('frontend.emirates_id_back')}}
                      </h3>
                      @foreach($user->traderImages  as $emirates_id_back)
                      @php($eib = pathinfo($emirates_id_back->image, PATHINFO_EXTENSION))
                        @if(!empty($emirates_id_back))
                          @if($eib == 'pdf')
                          <a href="{{$emirates_id_back->image}}" target="_blank" style="color: white; font-weight: bold;">
                              <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                          </a>
                          @else
                            @if($emirates_id_back->imageType == 'emirates_id_back')
                            <a href="{{$emirates_id_back->image}}" rel="lightbox[IDB]">
                                <img src="{{$emirates_id_back->image}}" alt="">
                            </a>
                            @endif
                          @endif
                        @endif
                      @endforeach
                  </div>

                  <div class="col-md-4 det">
                    <h3>
                        {{trans('frontend.passport_front')}}
                    </h3>
                    @foreach($user->traderImages  as $passport_front)
                    @php($pf = pathinfo($passport_front->image, PATHINFO_EXTENSION))
                      @if(!empty($passport_front))
                        @if($pf == 'pdf')
                        <a href="{{$passport_front->image}}" target="_blank" style="color: white; font-weight: bold;">
                            <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                        </a>
                        @else
                          @if($passport_front->imageType == 'passport_front')
                          <a href="{{$passport_front->image}}" rel="lightbox[IDPF]">
                              <img src="{{$passport_front->image}}" alt="">
                          </a>
                          @endif
                        @endif
                      @endif
                    @endforeach
                </div>

              
              <div class="col-md-4 det">
                <h3>
                    {{trans('frontend.passport_back')}}
                </h3>
                @foreach($user->traderImages  as $passport_back)
                @php($pb = pathinfo($passport_back->image, PATHINFO_EXTENSION))
                  @if(!empty($passport_back))
                    @if($pb == 'pdf')
                    <a href="{{$passport_back->image}}" target="_blank" style="color: white; font-weight: bold;">
                        <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                    </a>
                    @else
                      @if($passport_back->imageType == 'passport_back')
                      <a href="{{$passport_back->image}}"   rel="lightbox[IDPB]">
                          <img src="{{$passport_back->image}}"  alt="">
                      </a>
                      @endif
                    @endif
                  @endif
                @endforeach
            </div>

                <div class="col-md-4 det">
                  <h3>
                      {{trans('frontend.other_doc')}}
                  </h3>
                  

                  <ul class="users-list clearfix">
                  @foreach($user->traderImages  as $other_doc)
                  @if($other_doc->imageType == 'other_doc')
                  <li>
                  @php($od = pathinfo($other_doc->image, PATHINFO_EXTENSION))
                    @if(!empty($other_doc))
                       @if($od == 'pdf')
                      <a href="{{$other_doc->image}}" target="_blank" style="color: white; font-weight: bold;">
                          <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                      </a>
                        @else
                       
                        <a href="{{$other_doc->image}}"   rel="lightbox[OTH]">
                            <img src="{{$other_doc->image}}" alt="">
                        </a>
                        @endif
                      @endif
                    </li>
                    @endif
                  @endforeach
                </ul>
              </div>
                    {{--  <div class="col-md-4 det">
                        <h3>
                            {{trans('frontend.profile_passport')}}
                        </h3>
                        @if(!empty(Auth::guard('trader')->user()->passport))
                        @if($ext_passport == 'pdf')
                        <a href="{{Auth::guard('trader')->user()->passport}}" target="_blank" style="color: white; font-weight: bold;">
                            <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                        </a>
                        @else
                        <a href="{{Auth::guard('trader')->user()->passport}}"  data-lightbox="image-1" rel="lightbox[OTH]">
                            <img src="{{Auth::guard('trader')->user()->passport}}" data-lightbox="image-1" alt="">
                        </a>
                        @endif
                        @endif
                    </div>
                    <div class="col-md-4 det">
                        <h3>
                            {{trans('frontend.trade_license')}}
                        </h3>
                        @if(!empty(Auth::guard('trader')->user()->trade_license))
                        @if($ext_trade_license == 'pdf')
                        <a href="{{Auth::guard('trader')->user()->trade_license}}" target="_blank" style="color: white; font-weight: bold;">
                            <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                        </a>
                        @else
                       <a href="{{Auth::guard('trader')->user()->trade_license}}"  data-lightbox="image-3" rel="lightbox[OTH]">
                            <img src="{{Auth::guard('trader')->user()->trade_license}}"alt="">
                       </a>
                       @endif
                       @endif
                    </div>
                    <div class="col-md-4 det">
                         <h3>
                             {{trans('frontend.kyc')}}
                         </h3>
                        @if(!empty(Auth::guard('trader')->user()->kyc))
                             @if($ext_kyc == 'pdf')
                             <a href="{{Auth::guard('trader')->user()->kyc}}" target="_blank" style="color: white; font-weight: bold;">
                               <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                             </a>
                             @else
                             <div class="popup-gallery">
                                  <a href="{{Auth::guard('trader')->user()->kyc}}">
                                       <img class="img-responsive img-thumbnail prof-doc-img"  src="{{Auth::guard('trader')->user()->kyc}}"/>
                                  </a>
                             </div>
                             @endif
                          @endif
                    </div>  --}}

                    
                </div>
                {{--  <div class="row content">
                     <div class="col-md-4 det">
                          <h3>
                              {{trans('frontend.payment_receipt')}}
                          </h3>
                         @if(!empty(Auth::guard('trader')->user()->payment_receipt))
                              @if($ext_payment_receipt == 'pdf')
                              <a href="{{Auth::guard('trader')->user()->payment_receipt}}" target="_blank" style="color: white; font-weight: bold;">
                                <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                                </a>
                              @else
                              <div class="popup-gallery">
                                   <a href="{{Auth::guard('trader')->user()->payment_receipt}}">
                                        <img class="img-responsive img-thumbnail prof-doc-img"  src="{{Auth::guard('trader')->user()->payment_receipt}}"/>
                                   </a>
                              </div>
                              @endif
                           @endif
                     </div>
                     <div class="col-md-4 det">
                         <h3>
                             {{trans('frontend.profile_doc')}}
                         </h3>
                         @if(!empty(Auth::guard('trader')->user()->document))
                         @if($ext_document == 'pdf')
                         <a href="{{Auth::guard('trader')->user()->document}}" target="_blank" style="color: white; font-weight: bold;">   <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%"></a>
                         @else
                          <a href="{{Auth::guard('trader')->user()->document}}"  data-lightbox="image-2">
                             <img src="{{Auth::guard('trader')->user()->document}}" data-lightbox="image-2" alt="">
                         </a>
                         @endif
                         @endif
                     </div>
                </div>  --}}
            </div>
        </section>
        <link rel="stylesheet" href="{{asset('css/magnific-popup.css')}}">
        <style media="screen">
             .prof-doc-img {
                  max-width: 200px;
                  max-height: 200px;
                  object-fit: cover;
             }
        </style>
@endsection

@section('scripts')
<script type="text/javascript" src="{{asset('js/jquery.magnific-popup.min.js')}}"></script>
<script  type="text/javascript">
$(document).ready(function() {
   $('.popup-gallery').magnificPopup({
     delegate: 'a',
     type: 'image',
     tLoading: 'Loading image #%curr%...',
     mainClass: 'mfp-img-mobile',
     gallery: {
       enabled: true,
       navigateByImgClick: true,
       preload: [0,1] // Will preload 0 - before current, and 1 after the current image
     },
     image: {
       tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
       titleSrc: function(item) {
         // return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
       }
     }
   });
});



  <!-- <script>
    lightbox.option({
      'fadeDuration': 500
    })
</script> -->
  @endsection
