@extends('admin.layouts.master')
@section('content')
<div class="clearfix"></div>
    <div class="col-md-12 box box-success">
        @include('admin.includes.status-msg')
        <div class="container">
            <div class="row">
                <div class="col-md-4 popup-gallery">
                     <a href="{{$trader->image}}">
                          <img id="mob_display" class="prof-img" style="max-width: 300px;" src="{{$trader->image}}"/>
                    </a>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4 pull-right">
                        <h2>
                            <a href="{{url('traders/'.$trader->id.'/edit')}}"><i class="fa fa-edit"></i></a>
                            <a href="{{url('traders/destroy/'.$trader->id)}}"><i class="fa fa-trash"></i></a>
                            <a href="{{url('traders/credits/'.$trader->id)}}"><i class="fa fa-eye"></i></a>
                        </h2>
                    </div>
                    <div class="col-md-8">
                        <h4>Name : {{$trader->first_name}} {{$trader->last_name}}</h4>
                        <h4>{{$trader->email}}</h4>
                        <h4>{{$trader->phone}}</h4>
                        <h4>{{$trader->mobile}}</h4>
                        <h4>RTA Trade License Number : {{$trader->rta_file}}</h4>
                    </div>
                </div>
            </div>
            @php($ext_passport = pathinfo($trader->passport, PATHINFO_EXTENSION))
            @php($ext_trade_license = pathinfo($trader->trade_license, PATHINFO_EXTENSION))
            @php($ext_document = pathinfo($trader->document, PATHINFO_EXTENSION))
            @php($ext_kyc = pathinfo($trader->kyc, PATHINFO_EXTENSION))
            @php($ext_payment_receipt = pathinfo($trader->payment_receipt, PATHINFO_EXTENSION))
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-3">
                        <h3>Passport</h3>
                        @if($ext_passport == 'pdf')
                        <a href="{{$trader->passport}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" width="30%"></a>
                        @else
                        <div class="popup-gallery">
                             <a href="{{$trader->passport}}">
                                  <img class="img-responsive img-thumbnail prof-doc-img"  src="{{$trader->passport}}"/>
                             </a>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <h3>Trade License</h3>
                        @if($ext_trade_license == 'pdf')
                        <a href="{{$trader->trade_license}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" width="30%"></a>
                        @else
                        <div class="popup-gallery">
                             <a href="{{$trader->trade_license}}">
                                  <img class="img-responsive img-thumbnail prof-doc-img"  src="{{$trader->trade_license}}"/>
                             </a>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <h3>KYC</h3>
                        @if($ext_kyc == 'pdf')
                        <a href="{{$trader->kyc}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" width="30%"></a>
                        @else
                        <div class="popup-gallery">
                             <a href="{{$trader->kyc}}">
                                  <img class="img-responsive img-thumbnail prof-doc-img"  src="{{$trader->kyc}}"/>
                             </a>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <h3>Payment Receipt</h3>
                        @if($ext_payment_receipt == 'pdf')
                        <a href="{{$trader->payment_receipt}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" width="30%"></a>
                        @else
                        <div class="popup-gallery">
                             <a href="{{$trader->payment_receipt}}">
                              <img class="img-responsive img-thumbnail prof-doc-img"  src="{{$trader->payment_receipt}}"/>
                              </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                 <div class="col-md-12">
                      <div class="col-md-3">
                          <h3>Additional Document</h3>
                          @if($ext_document == 'pdf')
                          <a href="{{$trader->trade_license}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" width="30%"></a>
                          @else
                          <div class="popup-gallery">
                               <a href="{{$trader->document}}">
                                    <img class="img-responsive img-thumbnail prof-doc-img" src="{{$trader->document}}"/>
                               </a>
                          </div>

                          @endif
                      </div>
                 </div>
            </div>
            <div class="row">
                {{--<div class="col-md-6">
                   <h3>Total Payments : USD 542121</h3>
                    <h3>Outstanding Amount : USD 542121</h3>
                </div>--}}
                <div class="col-md-6">
                    <h3>Credit Limit : USD {{$trader->credit_limit}}</h3>
                    <h3>Deposit Amount : USD {{$trader->deposit_amount}}</h3>
                </div>
                <form class="form-horizontal" action="{{url('traders/'.$trader->id)}}" method="post">
                    <input name="_method" type="hidden" value="PATCH">
                    {{ csrf_field() }}
                  <div class="box-body">
                    <div class="form-group col-md-12">
                         <div class="row">
                                <div class="col-sm-10">
                                      <input class="form-control input-lg" type="text" name="credit_limit" placeholder="Credit Limit">
                                </div>
                           </div>
                    </div>
                    <div class="form-group col-md-12">
                         <div class="row">
                              <div class="col-sm-10">
                                   <input class="form-control input-lg" type="text" name="deposit_amount" placeholder="Deposit Amount">
                             </div>
                         </div>
                    </div>
                    <div class="form-group col-md-12">
                         <div class="row">
                              <div class="col-sm-2">
                                   <input type="submit" class="btn btn-block btn-primary btn-lg"/>
                             </div>
                         </div>
                    </div>
                  </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<style media="screen">
     .prof-doc-img {
          width: 200px;
          height: 200px;
          object-fit: cover;
     }
     .prof-img {
          width: 200px;
          height: 200px;
          object-fit: cover;
          border-radius: 50%;
     }
</style>
<link rel="stylesheet" href="{{asset('css/magnific-popup.css')}}">
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
</script>
@stop
