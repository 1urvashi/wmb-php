@extends('trader.layouts.master',['title'=>Auth::guard('trader')->user()->first_name,'class'=>'innerpage'])
@section('content')

<section class="section-profile p-10">
  <div class="container">
      <div class="user-information">
          <div class="user-information__list">
              <span class="user-information__title">{{trans('frontend.profile_first_name')}}</span>
              <span class="user-information__info">{{Auth::guard('trader')->user()->first_name}}</span>
          </div>
          <div class="user-information__list">
              <span class="user-information__title">{{trans('frontend.profile_last_name')}}</span>
              <span class="user-information__info">{{Auth::guard('trader')->user()->last_name}}</span>
          </div>
          <div class="user-information__list">
              <span class="user-information__title">{{trans('frontend.profile_email')}}</span>
              <span class="user-information__info">{{Auth::guard('trader')->user()->email}}</span>
          </div>
          <div class="user-information__list">
              <span class="user-information__title">{{trans('frontend.profile_phone')}}</span>
              <span class="user-information__info">{{Auth::guard('trader')->user()->phone}}</span>
          </div>
      </div>

      <div class="user-information uesr-identity">

          <div class="user-information__list emirateFrontMain">
              <span class="user-information__title"> {{trans('frontend.emirates_id_front')}}</span>
              <form action="{{route('uploadTraderImage',app()->getLocale())}}" class="form-imageUp" enctype="multipart/form-data">
                  <input type="file" name="file" id="" class="form-imageUp__input imgUpload"   data-type="emirates_id_front" data-main="emirateFrontMain">
                 
              </form>
              <?php
                $emirateFrontImages = $user->traderImages->filter(function ($value, $key) {
                    return $value->imageType == 'emirates_id_front';
                });
              ?>
              @foreach($emirateFrontImages  as $emirates_id_front)
              @php($eif = pathinfo($emirates_id_front->image, PATHINFO_EXTENSION))
               @if(!empty($emirates_id_front))
                  @if($eif == 'pdf')
                  <a href="{{$emirates_id_front->image}}" target="_blank" style="color: white; font-weight: bold;">
                      <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                  </a>
                  @else
                      
                      <figure>
                          <img src="{{$emirates_id_front->image}}" alt="user id">
                      </figure>
                      
                  @endif
               @endif
              @endforeach
          </div>

          <div class="user-information__list emirateBackMain">
              <span class="user-information__title"> {{trans('frontend.emirates_id_back')}}</span>
              <form action="{{route('uploadTraderImage',app()->getLocale())}}" class="form-imageUp" enctype="multipart/form-data">
                  <input type="file" name="file" id="" class="form-imageUp__input imgUpload"   data-type="emirates_id_back" data-main="emirateBackMain">
                  
              </form>
              <?php
                $emirateBackImages = $user->traderImages->filter(function ($value, $key) {

                    return $value->imageType == 'emirates_id_back';
                });
              ?>
              @foreach($emirateBackImages  as $emirates_id_back)
              @php($eib = pathinfo($emirates_id_back->image, PATHINFO_EXTENSION))
               @if(!empty($emirates_id_back))
                  @if($eib == 'pdf')
                  <a href="{{$emirates_id_back->image}}" target="_blank" style="color: white; font-weight: bold;">
                      <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                  </a>
                  @else
                      
                      <figure>
                          <img src="{{$emirates_id_back->image}}" alt="user id">
                      </figure>
                     
                  @endif
               @endif
              @endforeach
          </div>

          <div class="user-information__list passportFrontMain">
              <span class="user-information__title"> {{trans('frontend.passport_front')}}</span>
              <form action="{{route('uploadTraderImage',app()->getLocale())}}" class="form-imageUp" enctype="multipart/form-data">
                  <input type="file" name="file" id="" class="form-imageUp__input imgUpload"   data-type="passport_front" data-main="passportFrontMain">
                  
              </form>
              <?php
                $passportFrontImages = $user->traderImages->filter(function ($value, $key) {

                    return $value->imageType == 'passport_front';
                });
              ?>
              @foreach($passportFrontImages  as $passport_front)
              @php($pf = pathinfo($passport_front->image, PATHINFO_EXTENSION))
               @if(!empty($passport_front))
                  @if($pf == 'pdf')
                  <a href="{{$passport_front->image}}" target="_blank" style="color: white; font-weight: bold;">
                      <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                  </a>
                  @else
                     
                      <figure>
                          <img src="{{$passport_front->image}}" alt="user id">
                      </figure>
                     
                  @endif
               @endif
              @endforeach
          </div>

          <div class="user-information__list passportBackMain">
              <span class="user-information__title"> {{trans('frontend.passport_back')}}</span>
              <form action="{{route('uploadTraderImage',app()->getLocale())}}" class="form-imageUp" enctype="multipart/form-data">
                  <input type="file" name="file" id="" class="form-imageUp__input imgUpload"   data-type="passport_back" data-main="passportBackMain">
                  
              </form>
              <?php
                $passportBackImages = $user->traderImages->filter(function ($value, $key) {

                    return $value->imageType == 'passport_back';
                });
              ?>
              @foreach($passportBackImages  as $passport_back)
              @php($pb = pathinfo($passport_back->image, PATHINFO_EXTENSION))
               @if(!empty($passport_back))
                  @if($pb == 'pdf')
                  <a href="{{$passport_back->image}}" target="_blank" style="color: white; font-weight: bold;">
                      <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                  </a>
                  @else
                     
                      <figure>
                          <img src="{{$passport_back->image}}" alt="user id">
                      </figure>
                      
                  @endif
               @endif
              @endforeach
          </div>
          <?php
            $otherImages = $user->traderImages->filter(function ($value, $key) {

                return $value->imageType == 'other_doc';
            });
            
           ?>
          @if(count($otherImages) > 0)
          <?php $cnt = 0; ?>
          @foreach($otherImages as $key=>$other_doc)
          
          @php($od = pathinfo($other_doc->image, PATHINFO_EXTENSION))
         
          <div class="user-information__list otherMain" >
         
          @if($cnt == 0)
            <span class="user-information__title">
              {{trans('frontend.other_doc')}}
            </span>
            <form action="{{route('uploadTraderImage',app()->getLocale())}}" class="form-imageUp" enctype="multipart/form-data">
                <input type="file" name="file" id="other_file" class="form-imageUp__input imgUpload"   data-type="other_doc" data-main="otherMain"  @if(count($otherImages) >= 4) style="visibility: hidden;" @endif>
               
            </form>
            @else
            <span class="user-information__notitle">&nbsp;</span>
            @endif
              <?php $cnt++; ?>
             
              
                  @if($od == 'pdf')
                  <div class="other-remove-item">
                    <a href="{{$other_doc->image}}" target="_blank" style="color: white; font-weight: bold;">
                        <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                    </a>
                    <span class="trader-remove" data-id="{{$other_doc->id}}" onclick="removeItem('{{$other_doc->id}}',this)"><label for="imageUpload" class="" ><i class="fa fa-minus"></i></label></span>
                  </div>
                  @else
                      <div class="other-remove-item">
                      <figure>
                          <img src="{{$other_doc->image}}" alt="user id">
                      </figure>
                     
                      <span class="trader-remove" data-id="{{$other_doc->id}}" onclick="removeItem('{{$other_doc->id}}',this)"><label for="imageUpload" class="" ><i class="fa fa-minus"></i></label></span>
                    </div>
                  @endif
          </div>
          
         
          @endforeach
          @else
          <div class="user-information__list  otherMain">
          
              <span class="user-information__title"> {{trans('frontend.other_doc')}}</span>
              <form action="{{route('uploadTraderImage',app()->getLocale())}}" class="form-imageUp" enctype="multipart/form-data">
                  <input type="file" name="file" id="other_file" multiple class="form-imageUp__input imgUpload"  data-type="other_doc" data-main="otherEmpty">
                 
              </form>
              <div class="otherFirst"></div>
          </div>
          @endif
       


      </div>
  </div>
</section>

@append

@section('scripts')
<!-- <script type="text/javascript" src="{{asset('js/jquery.magnific-popup.min.js')}}"></script> -->
 <script type="text/javascript" src="{{asset('js/sweetalert.min.js')}}"></script>
<script  type="text/javascript">
  var labels = {
    'passport_front' : "{{trans('frontend.passport_front')}}",
    'passport_back' : "{{trans('frontend.passportback')}}",
    'emirates_id_front' : "{{trans('frontend.emirates_id_front')}}",
    'emirates_id_back' : "{{trans('frontend.emirates_id_back')}}",
    
  };

$(document).ready(function() {
   // $('.popup-gallery').magnificPopup({
   //   delegate: 'a',
   //   type: 'image',
   //   tLoading: 'Loading image #%curr%...',
   //   mainClass: 'mfp-img-mobile',
   //   gallery: {
   //     enabled: true,
   //     navigateByImgClick: true,
   //     preload: [0,1] // Will preload 0 - before current, and 1 after the current image
   //   },
   //   image: {
   //     tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
   //     titleSrc: function(item) {
   //       // return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
   //     }
   //   }
   // });

   $(document).on("change",".imgUpload",function(){
        var elem = $(this);
        var files = this.files;
        var file;
        var parent = $(elem).closest('.user-information__list');

        var type = $(elem).data('type');
        var mainClass = $(elem).data('main');
        if (files && files.length) {
          file = files[0];
          uploadFile(file,type,mainClass);
        }
      });
});

// single file upload function 
// function readURL(input, action_id) {
//   if (input.files && input.files[0]) {
//       var reader = new FileReader();
//       reader.onload = function (e) {
//           $('.' + action_id).css('background-image', 'url(' + e.target.result + ')');
//           $('.' + action_id).hide();
//           $('.' + action_id).fadeIn(650);
//       }
//       reader.readAsDataURL(input.files[0]);
//   }
// }
// jQuery(document).on('change', '.upload_file', function (e) {
//   var action_id = $(this).attr('data-file-name');
//   readURL(this, action_id);
//   $('.edit_'+action_id).show();
// });


function uploadFile(file,type,mainClass){
  var formData = new FormData();
  formData.append('file', file);
  formData.append('type', type);
  $('.ajax-success').hide();
  $('.ajax-error').hide();
  $.ajax({
         url : '{{route('uploadTraderImage',app()->getLocale())}}',
         type : 'POST',
         data : formData,
         processData: false,  // tell jQuery not to process the data
         contentType: false,  // tell jQuery not to set contentType
         dataType : 'json',
         success : function(data) {

            if(data.status == 'success'){
              
              if(mainClass == 'otherEmpty'){
                var inpt = { filePath : data.data.docUrl, id :data.data.docId };
                if(data.data.docType == 'pdf'){
                  var html = $("#other_first_pdf").html();                   
                }else{
                  var html = $("#other_first_image").html(); 
                }
                var rHtml = replaceTemplateContent(html,inpt);
                $(".otherFirst").replaceWith(rHtml);
                $("#other_file").data('main','otherMain');
              }else{
                
                if(mainClass == 'otherMain'){
                  var inpt = { filePath : data.data.docUrl, mainClass : mainClass, type : type, id :data.data.docId };
                  if(data.data.docType == 'pdf'){
                    var html = $("#other_single_pdf").html();                   
                  }else{
                    var html = $("#other_single_image").html(); 
                  }
                }else{
                  var label = labels[type];
                 
                  var inpt = { filePath : data.data.docUrl, mainClass : mainClass, type : type, label : label};
                  if(data.data.docType == 'pdf'){
                    var html = $("#single_pdf").html();                   
                  }else{
                    var html = $("#single_image").html(); 
                  }
                }
                
                var rHtml = replaceTemplateContent(html,inpt);
                if(mainClass == 'otherMain'){
                  $(rHtml).insertAfter($('.otherMain').last());
                }else{
                  $("."+mainClass).replaceWith(rHtml);
                }
              }
             
             $('.ajax-success').html(data.message).show();
             if($('.otherMain').length >= 4){
                $("#other_file").css("visibility", "hidden");
             }
            }else{
              $('.ajax-error').html(data.message).show();
            }
         }
  });
}
function replaceTemplateContent(template, data) {
    const pattern = /{\s*(\w+?)\s*}/g; // {property}
    return template.replace(pattern, (_, token) => data[token] || '');
}

function removeItem(id,ths){  
 var firstId = $(".otherMain").first().find(".trader-remove").data("id");
  swal({
      title: "{{trans('frontend.are_you_sure')}}",
      text: "{{trans('frontend.confirm_delete')}}",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
        $.ajax({
           url : '{{route('deleteTraderImage',app()->getLocale())}}',
           type : 'POST',
           data : {id : id},
           //processData: false,  // tell jQuery not to process the data
           //contentType: false,  // tell jQuery not to set contentType
           dataType : 'json',
           success : function(data) {
            if($('.otherMain').length >= 1){
              $(ths).closest('.otherMain').remove();
              if(firstId == id){
                location.reload();
              }
            }else{
              var html = $("#other_empty").html(); 
              $('.otherMain').replaceWith(html);
            }
            if($('.otherMain').length < 4){
                $("#other_file").val("");
                $("#other_file").css('visibility', 'visible');
             }
           }
       });
      } 
    });
}
</script>

<script id="single_pdf" type="text/template">
  <div class="user-information__list  {mainClass}">
              <span class="user-information__title"> {label}</span>
             

              <form action="{{route('uploadTraderImage',app()->getLocale())}}" class="form-imageUp" enctype="multipart/form-data">
                    <input type="file" name="file"  class="form-imageUp__input imgUpload"   data-type="{type}" data-main="{mainClass}">
                   
                </form>
              
                  <a href="{filePath}" target="_blank" style="color: white; font-weight: bold;">
                      <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                  </a>                  
          </div>
</script>

<script id="single_image" type="text/template">
  <div class="user-information__list {mainClass}">
              <span class="user-information__title"> {label}</span>
              <form action="{{route('uploadTraderImage',app()->getLocale())}}" class="form-imageUp" enctype="multipart/form-data">
                    <input type="file" name="file"  class="form-imageUp__input imgUpload"   data-type="{type}" data-main="{mainClass}">
                   
                </form>
                
                  <figure>
                      <img src="{filePath}" alt="user id">
                  </figure>               
          </div>
</script>
<script id="other_first_pdf" type="text/template">
  <div class="other-remove-item">
          <a href="{filePath}" target="_blank" style="color: white; font-weight: bold;">
              <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
          </a> 
          
          <span class="trader-remove" data-id="{id}" onclick="removeItem('{id}',this)"><label for="imageUpload" class="" ><i class="fa fa-minus"></i></label></span>
    </div>
</script>

<script id="other_first_image" type="text/template">
  <div class="other-remove-item">
          <figure>
              <img src="{filePath}" alt="user id">
          </figure> 
          <span class="trader-remove" data-id="{id}" onclick="removeItem('{id}',this)"><label for="imageUpload" class="" ><i class="fa fa-minus"></i></label></span>
    </div>
         
</script>

<script id="other_single_pdf" type="text/template">
  <div class="user-information__list  otherMain">
              <span class="user-information__notitle"> &nbsp;</span>
                 <div class="other-remove-item">
                  <a href="{filePath}" target="_blank" style="color: white; font-weight: bold;">
                      <img src="{{url('img/pdf-icon.png')}}" alt="" width="60%">
                  </a>
                  <span class="trader-remove" data-id="{id}" onclick="removeItem('{id}',this)"><label for="imageUpload" class="" ><i class="fa fa-minus"></i></label></span>   
              </div>               
          </div>
</script>

<script id="other_single_image" type="text/template">
  <div class="user-information__list otherMain">
              <span class="user-information__notitle"> &nbsp;</span>
              <div class="other-remove-item">
                  <figure>
                      <img src="{filePath}" alt="user id">
                  </figure>   
                  <span class="trader-remove" data-id="{id}" onclick="removeItem('{id}',this)"><label for="imageUpload" class="" ><i class="fa fa-minus"></i></label></span>  
                  </div>          
          </div>
</script>

<script id="other_empty" type="text/template">
  <div class="user-information__list  otherMain">
          
              <span class="user-information__title"> {{trans('frontend.other_doc')}}</span>
              <form action="{{route('uploadTraderImage',app()->getLocale())}}" class="form-imageUp" enctype="multipart/form-data">
                  <input type="file" name="file" id="other_file" multiple class="form-imageUp__input imgUpload"  data-type="other_doc" data-main="otherEmpty">
              </form>
              <div class="otherFirst"></div>
          </div>
</script>
  <!-- <script>
    lightbox.option({
      'fadeDuration': 500
    })
</script> -->
  @endsection
