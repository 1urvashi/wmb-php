@extends('admin.layouts.master')
@section('content')
@php($user = Auth::guard('admin')->user())
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Trader</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            @include('admin.includes.status-msg')
            <form role="form" enctype="multipart/form-data" method="post"
                action="@if(isset($trader)) {{url('traders/'.$trader->id)}} @else {{url('traders')}} @endif">
                @if(isset($trader)) <input name="_method" type="hidden" value="PUT"> @endif
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="col-md-6">
                        <div class="row " style="display: none;">
                            <div class="col-xs-5 left">
                                <label>Upload Profile Image @if(!isset($trader))<span class="req">*</span>@endif
                                    <small>( Minimum 1200 x 900 pixels)</small></label>
                            </div>
                            <div class="col-xs-7 right">
                                <div class="form-control file-img">
                                    <input type="file" name="images[image]" accept="image/*" class="file-up">
                                </div>
                                <div class="col-md-12">
                                    @if(isset($trader))
                                    <img id="mob_display" style="max-width: 100px;" src="{{$trader->image}}" />
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>First Name <span class="req">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                value="@if(isset($trader)){{$trader->first_name}}@else{{old('first_name')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Last Name <span class="req">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                value="@if(isset($trader)){{$trader->last_name}}@else{{old('last_name')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Phone <span class="req">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                   value="@if(isset($trader)){{$trader->phone}}@else{{old('phone')}}@endif">
                        </div>

                        {{--  <div class="form-group">
                            <label>Emirates Id Front <span class="req">*</span></label>
                            <input type="file" name="images['emirates_id_front']"
                                        accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up"  >
                        </div>  --}}

                        {{-- @if(isset($trader))
                        @php($ext_emirates_id_front = pathinfo($image->image, PATHINFO_EXTENSION))
                        @php($ext_trade_license = pathinfo($image->image, PATHINFO_EXTENSION))
                        @php($ext_document = pathinfo($image->image, PATHINFO_EXTENSION))
                        @php($ext_kyc = pathinfo($image->image, PATHINFO_EXTENSION))
                        @php($ext_payment_receipt = pathinfo($image->image, PATHINFO_EXTENSION))
                        @endif --}}

                    <div style="" class="row ">
                        <div class="col-xs-5 left">
                            <label>Emirates Id Front <span class="req">*</span></label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="trader_images[emirates_id_front]"
                                    accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up eid"  >
                            </div>
                             <div class="col-xs-12">
                                @if(isset($trader))
                                <ul class="users-list clearfix image-eid">
                                @foreach($trader->traderImages  as $image)
                               
                                @if($image->imageType == 'emirates_id_front')
                                @php($ext_emirates_id_front= pathinfo($image->image, PATHINFO_EXTENSION))
                                <li>
                                    @if($ext_emirates_id_front == 'pdf')
                                    <a href="{{$image->image}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" style="min-width: 100px;
                                        min-height: 170px;"></a>

                                    <a onclick="return confirm('Are you sure you want to delete this Pdf?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>

                                    @else
                                    <img style="min-width: 100px;
                                    min-height: 170px;" src="{{$image->image}}"
                                    data-darkbox="{{$image->image}}" alt="Watch Image" class="img-eid">
                                    <a onclick="return confirm('Are you sure you want to delete this image?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>
                                 @endif

                            </li>
                                @endif
                                @endforeach
                                </ul>
                                @endif
                                
                            </div>
                        </div>
                    </div>

                    <div style="" class="row ">
                        <div class="col-xs-5 left">
                            <label>Passport Front <span class="req">*</span></label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="trader_images[passport_front]"
                                    accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up pfid" >
                            </div>
                            <div class="col-md-12">
                                @if(isset($trader))
                                <ul class="users-list clearfix image-pfid">
                                @foreach($trader->traderImages  as $image)
                                
                                @if($image->imageType == 'passport_front')
                                @php($ext_passport_front = pathinfo($image->image, PATHINFO_EXTENSION))
                                <li>
                                    @if($ext_passport_front == 'pdf')
                                    <a href="{{$image->image}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" style="min-width: 100px;
                                        min-height: 170px;"></a>

                                    <a onclick="return confirm('Are you sure you want to delete this Pdf?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>

                                    @else

                                    <img style="min-width: 100px;
                                    min-height: 170px;" src="{{$image->image}}"
                                    data-darkbox="{{$image->image}}" alt="Watch Image">
                                    <a onclick="return confirm('Are you sure you want to delete this image?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>

                                     @endif

                                </li>
                                @endif
                                @endforeach
                                </ul>
                                @endif
                                
                            </div>
                        </div>
                    </div>

                    <div style="" class="row ">
                        <div class="col-xs-5 left">
                            <label>Other Docs</label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="trader_images[other_doc][]"
                                accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up" multiple>
                            </div>
                            <div class="col-md-12">
                                @if(isset($trader))
                                <ul class="users-list clearfix image-pfid">
                                @foreach($trader->traderImages  as $image)
                              
                                @if($image->imageType == 'other_doc')
                                @php($ext_other_doc = pathinfo($image->image, PATHINFO_EXTENSION))

                                <li>
                                    @if($ext_other_doc == 'pdf')
                                    <a href="{{$image->image}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" style="min-width: 100px;
                                        min-height: 170px;"></a>

                                    <a onclick="return confirm('Are you sure you want to delete this Pdf?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>

                                    @else
                                    
                                    <img style="min-width: 100px;
                                    min-height: 170px;" src="{{$image->image}}"
                                    data-darkbox="{{$image->image}}" alt="Watch Image">
                                    <a onclick="return confirm('Are you sure you want to delete this image?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>
                                @endif

                            </li>
                                @endif
                                @endforeach
                                </ul>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                   

                   
                        {{--<div class="form-group">
                           <label>Branch</label>
                            <select name="dealer_id" class="form-control select2" >
                                @foreach($dealers as $dealer)
                                <option @if(isset($trader) && ($trader->dealer_id == $dealer->id)) selected @endif value="{{$dealer->id}}">{{$dealer->name}}
                        </option>
                        @endforeach
                        </select>
                    </div>--}}
                    <?php
                    /*
                         $drm  = new \App\User();
                         ?>
                    @if($user->role == $drm->getDRM())
                    <input type="hidden" name="dmr_id" value="{{$user->id}}">
                    @else
                    <div class="form-group">
                        <label>DRMs <span class="req">*</span></label>
                        <select name="dmr_id" class="form-control select2">
                            <option value="">Choose DRM</option>
                            @foreach($drms as $data)
                            <?php
                                      $otext = $data->name;

                                      switch($data->type){
                                          case $gType['DRM']:
                                              $otext .= ' (DRM)';
                                              break;
                                          case $gType['HEAD_DRM']:
                                              $otext .= ' (HEAD OF DRM)';
                                              break;
                                          default:
                                              break;
                                      }
                                      ?>
                            <option @if(isset($trader) && ($trader->dmr_id == $data->id)) selected @else
                                {{ old('dmr_id') == $data->id ? 'selected' : '' }} @endif value="{{$data->id}}">
                                {{ $otext }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if($user->type == config('globalConstants.TYPE.ONBOARDER'))
                    <input type="hidden" name="onboarder_id" value="{{$user->id}}">
                    @else
                    <div class="form-group">
                        <label>Onboarder <span class="req">*</span></label>
                        <select name="onboarder_id" class="form-control select2">
                            <option value="">Choose Onboarder</option>
                            @foreach($onboarders as $data)
                            <option @if(isset($trader) && ($trader->onboarder_id == $data->id)) selected @else
                                {{ old('onboarder_id') == $data->id ? 'selected' : '' }} @endif
                                value="{{$data->id}}">{{$data->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <?php
                    */ ?>

                    <div style="display: none;" class="form-group">
                        <label>Country <span class="req">*</span></label>
                        <select name="country_id" class="form-control select2">
                            <option value="">Choose Country</option>
                            @foreach($countries as $data)
                            <option @if(isset($trader) && ($trader->country_id == $data->id)) selected @else
                                {{ old('country_id') == $data->id ? 'selected' : '' }} @endif
                                value="{{$data->id}}">{{$data->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: none;" class="form-group">
                        <label>Emirates <span class="req">*</span></label>
                        <select name="emirate_id" class="form-control select2">
                            <option value="">Choose Emirate</option>
                            @foreach($emirates as $data)
                            <option @if(isset($trader) && ($trader->emirate_id == $data->id)) selected @else
                                {{ old('emirate_id') == $data->id ? 'selected' : '' }} @endif
                                value="{{$data->id}}">{{$data->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: none;" class="form-group">
                        <label>Company Name <span class="req">*</span></label>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                            value="@if(isset($trader)){{$trader->company_name}}@else{{old('company_name')}}@endif">
                    </div>
                    <div style="display: none;" class="form-group">
                        <label>Trade License No <span class="req">*</span></label>
                        <input type="text" class="form-control" id="trade_license_no" name="trade_license_no"
                            value="@if(isset($trader)){{$trader->trade_license_no}}@else{{old('trade_license_no')}}@endif">
                    </div>

                </div>
                <div class="col-md-6">
                  <div style="display: none;" class="form-group">
                      <label>Tax Registration No <span class="req">*</span></label>
                      <input type="text" class="form-control" id="tax_registration_no" name="tax_registration_no"
                          value="@if(isset($trader)){{$trader->tax_registration_no}}@else{{old('tax_registration_no')}}@endif">
                  </div>
                  <div style="display: none;" class="form-group">
                      <label>Emirates Amount <span class="req">*</span></label>
                      <input type="text" class="form-control" id="emirates_id" name="emirates_id"
                          value="@if(isset($trader)){{$trader->emirates_id}}@else{{old('emirates_id')}}@endif">
                  </div>
                  <div style="" class="form-group">
                    <label>Estimated Amount <span class="req">*</span></label>
                    <input type="number" class="form-control" id="estimated_amount" name="estimated_amount"
                        value="@if(isset($trader)){{$trader->estimated_amount}}@else{{old('estimated_amount')}}@endif">
                </div>
                  <div style="display: none;" class="form-group">
                      <label>Expiry <span class="req">*</span></label>
                      <input type="text" class="form-control datepicker" id="expiry" name="expiry"
                          value="@if(isset($trader)){{$trader->emiratesIdExpiry}}@else{{old('expiry')}}@endif">
                  </div>
                  <div style="display: none;" class="form-group">
                      <label>P.O. Box <span class="req">*</span></label>
                      <input type="text" class="form-control" id="post_code" name="post_code"
                          value="@if(isset($trader)){{$trader->post_code}}@else{{old('post_code')}}@endif">
                  </div>
                    <div class="form-group">
                        <label>Email <span class="req">*</span></label>
                        <input type="text" class="form-control" id="email" name="email"
                               value="@if(isset($trader)){{$trader->email}}@else{{old('email')}}@endif">
                    </div>
                    <div class="form-group">
                        <label>Password @if(!isset($trader))<span class="req">*</span>@endif</label>
                        <input type="password" class="form-control" id="email" name="password">
                    </div>

                  <div style="display: none;" class="form-group">
                      <label>Mobile</label>
                      <input type="text" class="form-control" id="mobile" name="mobile"
                          value="@if(isset($trader)){{$trader->mobile}}@else{{old('mobile')}}@endif">
                  </div>

                    @if(isset($trader))
                    @php($ext_passport = pathinfo($trader->passport, PATHINFO_EXTENSION))
                    @php($ext_trade_license = pathinfo($trader->trade_license, PATHINFO_EXTENSION))
                    @php($ext_document = pathinfo($trader->document, PATHINFO_EXTENSION))
                    @php($ext_kyc = pathinfo($trader->kyc, PATHINFO_EXTENSION))
                    @php($ext_payment_receipt = pathinfo($trader->payment_receipt, PATHINFO_EXTENSION))
                    @endif

                    <div style="" class="row ">
                        <div class="col-xs-5 left">
                            <label>Emirates Id Back <span class="req">*</span></label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="trader_images[emirates_id_back]"
                                    accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up ebid">
                            </div>
                             <div class="col-md-12">
                                @if(isset($trader))
                                <ul class="users-list clearfix image-ebid">
                                @foreach($trader->traderImages  as $image)
                                @if($image->imageType == 'emirates_id_back')
                                @php($ext_emirates_id_back= pathinfo($image->image, PATHINFO_EXTENSION))
                                <li>
                                    @if($ext_emirates_id_back == 'pdf')
                                    <a href="{{$image->image}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" style="min-width: 100px;
                                        min-height: 170px;"></a>

                                    <a onclick="return confirm('Are you sure you want to delete this Pdf?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>

                                    @else
                                    <img style="min-width: 100px;
                                    min-height: 170px;" src="{{$image->image}}"
                                    data-darkbox="{{$image->image}}" alt="Watch Image">
                                    <a onclick="return confirm('Are you sure you want to delete this image?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>
                                     @endif

                            </li>
                            @endif
                                @endforeach
                                </ul>
                                @endif
                                
                            </div>
                        </div>
                    </div>

                    <div style="" class="row ">
                        <div class="col-xs-5 left">
                            <label>Passport Back <span class="req">*</span></label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="trader_images[passport_back]"
                                    accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up pbid">
                            </div>
                            <div class="col-md-12">
                                @if(isset($trader))
                                <ul class="users-list clearfix image-pbid">
                                @foreach($trader->traderImages  as $image)
                                @if($image->imageType == 'passport_back')
                                @php($ext_passport_back= pathinfo($image->image, PATHINFO_EXTENSION))
                                <li>
                                    @if($ext_passport_back == 'pdf')
                                    <a href="{{$image->image}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" style="min-width: 100px;
                                        min-height: 170px;"></a>

                                    <a onclick="return confirm('Are you sure you want to delete this Pdf?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>

                                    @else
                                    <img style="min-width: 100px;
                                    min-height: 170px;" src="{{$image->image}}"
                                    data-darkbox="{{$image->image}}" alt="Watch Image">
                                    <a onclick="return confirm('Are you sure you want to delete this image?');" style="margin-top: 10px;" href="{{url('remove-trader-data/'.$image->imageType.'/'.$image->id)}}" class="btn btn-danger">Remove</a>
                                @endif

                            </li>
                                @endif
                                @endforeach
                                </ul>
                                @endif
                                
                            </div>
                        </div>
                    </div>




                    <div style="display: none;" class="row ">
                        <div class="col-xs-5 left">
                            <label>Upload Passport/ID @if(!isset($trader))<span class="req">*</span>@endif
                                <!-- <small>( Minimum 1200 x 900 pixels)</small>--></label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="images[passport]"
                                    accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up">
                            </div>
                            <div class="col-md-12">
                                @if(isset($trader))
                                @if($ext_passport == 'pdf')
                                <a href="{{$trader->passport}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}"
                                        alt="" width="18%"></a>
                                @else
                                <img id="mob_display" style="max-width: 100px;" src="{{$trader->passport}}" />
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="display: none;" class="row ">
                        <div class="col-xs-5 left">
                            <label>Upload Trader License @if(!isset($trader))<span class="req">*</span>@endif
                                <!-- <small>( Minimum 1200 x 900 pixels)</small>--></label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="images[trade_license]"
                                    accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up">
                            </div>
                            <div class="col-md-12">
                                @if(isset($trader))
                                @if($ext_trade_license == 'pdf')
                                <a href="{{$trader->trade_license}}" target="_blank"> <img
                                        src="{{url('img/pdf-icon.png')}}" alt="" width="18%"></a>
                                @else
                                <img id="mob_display" style="max-width: 100px;" src="{{$trader->trade_license}}" />
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="display: none;" class="row ">
                        <div class="col-xs-5 left">
                            <label>KYC @if(!isset($trader))<span class="req">*</span>@endif
                                <!-- <small>( Minimum 1200 x 900 pixels)</small>--></label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="images[kyc]"
                                    accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up">
                            </div>
                            <div class="col-md-12">
                                @if(isset($trader))
                                @if($ext_kyc == 'pdf')
                                <a href="{{$trader->kyc}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}"
                                        alt="" width="18%"></a>
                                @else
                                <img id="mob_display" style="max-width: 100px;" src="{{$trader->kyc}}" />
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="display: none;" class="row ">
                        <div class="col-xs-5 left">
                            <label>Payment Receipt @if(!isset($trader))<span class="req">*</span>@endif
                                <!-- <small>( Minimum 1200 x 900 pixels)</small>--></label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="images[payment_receipt]"
                                    accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up">
                            </div>
                            <div class="col-md-12">
                                @if(isset($trader))
                                @if($ext_payment_receipt == 'pdf')
                                <a href="{{$trader->payment_receipt}}" target="_blank"> <img
                                        src="{{url('img/pdf-icon.png')}}" alt="" width="18%"></a>
                                @else
                                <img id="mob_display" style="max-width: 100px;" src="{{$trader->payment_receipt}}" />
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="display: none;" class="row ">
                        <div class="col-xs-5 left">
                            <label>Additional Document @if(!isset($trader))<span class="req">*</span>@endif
                                <!-- <small>( Minimum 1200 x 900 pixels)</small>--> </label>
                        </div>
                        <div class="col-xs-7 right">
                            <div class="form-control file-img">
                                <input type="file" name="images[document]"
                                    accept="image/jpeg,image/gif,image/png,application/pdf" class="file-up">
                            </div>
                            <div class="col-md-12">
                                @if(isset($trader))
                                @if($ext_document == 'pdf')
                                <a href="{{$trader->document}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}"
                                        alt="" width="18%"></a>
                                @else
                                <img id="mob_display" style="max-width: 100px;" src="{{$trader->document}}" />
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="display: none;">
                    <div class="panel panel-default">
                        <div class="panel-heading">Dealer Capability and Preferences</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Business Size (No of Watches) <span class="req">*</span></label>
                                        <select name="business_size" class="form-control select2">
                                            <option value="">Choose Business Size</option>
                                            <option value="0-10" @if(isset($trader) && ($trader->kycBusinessLowSize.'-'.$trader->kycBusinessUpSize == '0-10')) selected @else
                                                {{ old('business_size') == '0-10' ? 'selected' : '' }}@endif>0-10</option>
                                            <option value="10-30" @if(isset($trader) && ($trader->kycBusinessLowSize.'-'.$trader->kycBusinessUpSize == '10-30')) selected @else
                                                {{ old('business_size') == '10-30' ? 'selected' : '' }}@endif>10-30</option>
                                            <option value="30-50" @if(isset($trader) && ($trader->kycBusinessLowSize.'-'.$trader->kycBusinessUpSize == '30-50')) selected @else
                                                {{ old('business_size') == '30-50' ? 'selected' : '' }}@endif>30-50</option>
                                            <option value="50-100" @if(isset($trader) && ($trader->kycBusinessLowSize.'-'.$trader->kycBusinessUpSize == '50-100')) selected @else
                                                {{ old('business_size') == '50-100' ? 'selected' : '' }}@endif>50-100</option>
                                            <option value="100-500" @if(isset($trader) && ($trader->kycBusinessLowSize.'-'.$trader->kycBusinessUpSize == '100-500')) selected @else
                                                {{ old('business_size') == '100-500' ? 'selected' : '' }}@endif>100-500</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Credit Limit <span class="req">*</span></label>
                                        <select name="kyc_credit_limit" class="form-control select2">
                                            <option value="">Choose Credit Limit</option>
                                            <option value="60000" @if(isset($trader) && ($trader->kycCreditLimit == '60000')) selected @else
                                                {{ old('kyc_credit_limit') == '60000' ? 'selected' : '' }}@endif>up to 60 000 Dhs</option>
                                            <option value="125000" @if(isset($trader) && ($trader->kycCreditLimit == '125000')) selected @else
                                                {{ old('kyc_credit_limit') == '125000' ? 'selected' : '' }}@endif>up to 125 000 Dhs</option>
                                            <option value="250000" @if(isset($trader) && ($trader->kycCreditLimit == '250000')) selected @else
                                                {{ old('kyc_credit_limit') == '250000' ? 'selected' : '' }}@endif>up to 250 000 Dhs</option>
                                            <option value="500000" @if(isset($trader) && ($trader->kycCreditLimit == '500000')) selected @else
                                                {{ old('kyc_credit_limit') == '500000' ? 'selected' : '' }}@endif>up to 500 000 Dhs</option>
                                            <option value="1000000" @if(isset($trader) && ($trader->kycCreditLimit == '1000000')) selected @else
                                                {{ old('kyc_credit_limit') == '1000000' ? 'selected' : '' }}@endif>up to 1 million Dhs</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group icheck-custom">
                                        <label>Age of watch <span class="req">*</span></label>
                                        <!-- checkbox -->
                                        <select name="age_of_car" class="form-control select2">
                                            <option value="">Choose</option>
                                            <option value="3" @if(isset($trader) && ($trader->kycCarAge == 3)) selected @else
                                                {{ old('age_of_car') == 3 ? 'selected' : '' }}@endif>Up to 3 years</option>
                                            <option value="5" @if(isset($trader) && ($trader->kycCarAge == 5)) selected @else
                                                {{ old('age_of_car') == 5 ? 'selected' : '' }}@endif>Up to 5 years</option>
                                            <option value="10" @if(isset($trader) && ($trader->kycCarAge == 10)) selected @else
                                                {{ old('age_of_car') == 10 ? 'selected' : '' }}@endif>Up to 10 years</option>
                                            <option value="20" @if(isset($trader) && ($trader->kycCarAge == 20)) selected @else
                                                {{ old('age_of_car') == 20 ? 'selected' : '' }}@endif>Up to 20 years</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row">


                                <div class="col-md-4">
                                    <div class="form-group icheck-custom">
                                        <label>Target market <span class="req">*</span></label>
                                        @foreach($markets as $market)
                                        <!-- checkbox -->
                                        <label>
                                            <input type="checkbox" name="target_market[]" class="minimal" value="{{$market->id}}"
                                            @if(!empty($marketsArray) && in_array($market->id, $marketsArray)) checked  @endif>
                                            {{$market->title}}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group icheck-custom">
                                        <label>Watch condition <span class="req">*</span></label>
                                        <!-- checkbox -->
                                        @foreach($carConditions as $carCondition)
                                        <label>
                                            <input type="checkbox" name="car_condition[]" class="minimal" value="{{$carCondition->id}}"
                                            @if(!empty($carConditionIdArray) && in_array($carCondition->id, $carConditionIdArray)) checked  @endif>
                                            {{$carCondition->title}}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                              <div class="col-md-4">
                                  <div class="form-group icheck-custom">
                                      <label>Specifications <span class="req">*</span></label>
                                      <!-- checkbox -->
                                      @foreach($specifications as $specification)
                                      <label>
                                          <input type="checkbox" name="specifications[]" class="minimal" value="{{$specification->id}}"
                                          @if(!empty($specificationIdArray) && in_array($specification->id, $specificationIdArray)) checked  @endif>
                                          {{$specification->title}}
                                      </label>
                                      @endforeach

                                  </div>
                              </div>



                                <div class="col-md-4">
                                    <div class="form-group icheck-custom">
                                        <label>Make of watch <span class="req">*</span></label>
                                        <!-- checkbox -->
                                        @foreach($carMakes as $carMake)
                                        <label>
                                            <input type="checkbox" id="{{strtolower($carMake->title)}}" name="make_cars[]" class="minimal" value="{{$carMake->id}}"
                                            @if(!empty($carMakeIdArray) && in_array($carMake->id, $carMakeIdArray)) checked  @endif>
                                            {{$carMake->title}}
                                        </label>
                                        @endforeach

                                        <input type="text" name="other_value" id="other_value" class="form-control"
                                             @if(!empty($otherValue->otherTitle))style="display:block;" @endif
                                             value="@if(!empty($otherValue->otherTitle)){{$otherValue->otherTitle}}@endif">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <button type="submit" class="btn btn-primary form-submit">Submit</button>
            <a id="trader-cancel-btn" href="{{url('traders')}}" class="btn btn-danger">Cancel</a>
        </div>
        </form>
    </div>
</div>
</div>
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('plugins/iCheck/all.css') }}">
<style>
    .icheck-custom label {
        width: 100%;
        margin: 5px 0;
    }

    #other_value {
        display: none;
    }
</style>
@endsection
@push('scripts')
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script>
$('.datepicker').datepicker({
format: 'yyyy-mm-dd'
        });
    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });

    $('#other').on('ifChecked', function (event) {
        $('#other_value').show();
        $("#other_value").removeAttr("disabled");
    });

    $('#other').on('ifUnchecked', function (event) {
        $('#other_value').hide();
        $('#other_value').attr("disabled", "disabled");
    });

    $('form').submit(function () {
        $('form button').attr('disabled', true);
        $('form a#trader-cancel-btn').attr('disabled', true);
    })

   

    $(document).ready(function(){
        var efid =  $('.image-eid').find('li').length;
        var ebid =  $('.image-ebid').find('li').length;
        var pfid =  $('.image-pfid').find('li').length;
        var pbid =  $('.image-pbid').find('li').length;
        if(efid == 0){
            $('.eid').attr('required',true);
        }else{
            $('.eid').attr('required',false);
        }

        if(pbid == 0){
            $('.pbid').attr('required',true);
        }else{
            $('.pbid').attr('required',false);
        }

        if(ebid == 0){
            $('.ebid').attr('required',true);
        }else{
            $('.ebid').attr('required',false);
        }

        if(pfid == 0){
            $('.pfid').attr('required',true);
        }else{
            $('.pfid').attr('required',false);
        }
    });
</script>
@endpush
