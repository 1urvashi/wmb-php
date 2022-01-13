<!DOCTYPE html>
<html @if(session()->get('language') == 'ar') dir="rtl" lang="ar" @else lang="en"  @endif>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <link rel="stylesheet" href="{{URL::asset('css/frontend/assets/css/main.min.css')}}" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
       
        .avatar-editt {
            display: inline-block;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            padding: 7px;
            right: -1rem;
            background-color: #fff;
            position: absolute;
            font-size: 1.4rem;
            top: 3px;
            line-height: 0.4;
            text-align: center;
            cursor: pointer;
            z-index: 3;
            }
            .avatar-editt .fa{
                color:#000;
                
            }
            html[dir="rtl"] .avatar-editt {
            right: unset;
            left: -1rem;
            }
        .avatar-remove {
            display: inline-block;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            padding: 5px;
            right: -1.3rem;
            background-color: #fff;
            position: absolute;
            font-size: 3rem;
            top: 3px;
            line-height: 0.4;
            text-align: center;
            cursor: pointer;
            z-index: 3;
            } 

            html[dir="rtl"] .avatar-remove {
            right: unset;
            left: -1rem;
            }

      .avatar-remove a {
            color: #757575;
        } 
    </style>
</head>
        <body>
            <div class="log-in-wrap">
            @include('trader.includes.status-msg')
                <div class="log-in-wrap__wrap">
                    <div class="log-in-wrap__log-in sign-up">
                        <div class="log-in-wrap__start ">
                            <img src="{{URL::asset('css/frontend/assets/img/logo.svg')}}" alt="wmb logo" />
                        </div>
                        <div class="log-in-wrap__end ">
                        

                            <span class="title">{{trans('frontend.registration')}}</span>
                            <form role="form" enctype="multipart/form-data"
                            action="{{url(session()->get('language').'/trader/register')}}" method="post" id="signupForm"
                            >

                                <input type="text" name="first_name" class="form-log-in__input"
                                placeholder="{{trans('frontend.p_first_name')}}"  value="{{ old('first_name') }}" >

                                <input type="text" name="last_name" class="form-log-in__input"
                                placeholder="{{trans('frontend.p_last_name')}}"  value="{{ old('last_name') }}" >

                                <input type="email" name="email" class="form-log-in__input" placeholder="{{trans('frontend.p_email')}}"
                                value="{{ old('email') }}">

                                <input type="password" name="password" class="form-log-in__input"
                                placeholder="{{trans('frontend.p_pwd')}}" id="password" value="{{ old('password') }}">

                                <input type="text" name="phone" class="form-log-in__input number" placeholder="{{trans('frontend.p_phone')}}" value="{{ old('phone') }}">

                                <input type="text" name="estimated_amount" class="form-log-in__input number"
                                placeholder="{{trans('frontend.p_estimated_amount')}}" value="{{ old('estimated_amount') }}">

                                <div class="documets-add">
                                    <div class="documets-add__group">
                                        <div class="documets-add__start ">
                                            <div class="form-group has-feedback">
                                                <label id="emirates-img">{{trans('frontend.p_emirates_id_front')}}</label>
                                                <div class="avatar-upload">
                                                    <div class="avatar-edit">
                                                        <input type='file' class="upload_file efid" data-file-name="file1"
                                                            name="trader_images[emirates_id_front]"
                                                            value="{{ old('images.emirates_id_front') }}" id="imageUpload"
                                                            accept=".png, .jpg, .jpeg" />
                                                        {{-- <label for="imageUpload"></label> --}}
                                                    </div>

                                                    <div class="avatar-preview">
                                                        <div class="file1" onclick='$("#imageUpload").click()' >

                                                        </div>
                                                        <label for="imageUpload" class="avatar-editt edit_file1" style="display:none;" ><i
                                                                class="fa fa-pencil"></i></label>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="documets-add__end">
                                            <div class="form-group has-feedback">
                                                <label id="emirates-img">{{trans('frontend.p_emirates_id_back')}}</label>
                                                <div class="avatar-upload">
                                                    <div class="avatar-edit">
                                                        <input type='file' class="upload_file ebid" data-file-name="file2"
                                                            name="trader_images[emirates_id_back]" id="imageUpload1"
                                                            accept=".png, .jpg, .jpeg" />
                                                        {{-- <label for="imageUpload1"></label> --}}
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div class="file2" onclick='$("#imageUpload1").click()'>
                                                        </div>
                                                        <label for="imageUpload1" class="avatar-editt edit_file2" style="display:none;"><i
                                                                class="fa fa-pencil"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="documets-add__group">
                                        <div class="documets-add__start ">
                                            <div class="form-group has-feedback">
                                                <label id="emirates-img">{{trans('frontend.p_passport_front')}}</label>
                                                <div class="avatar-upload">
                                                    <div class="avatar-edit">
                                                        <input type='file' class="upload_file pfid" data-file-name="file3"
                                                            name="trader_images[passport_front]" id="imageUpload2"
                                                            accept=".png, .jpg, .jpeg" />
                                                        {{-- <label for="imageUpload2"></label> --}}
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div class="file3" onclick='$("#imageUpload2").click()'>
                                                        </div>
                                                        <label for="imageUpload2" class="avatar-editt edit_file3" style="display:none;"><i
                                                                class="fa fa-pencil"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="documets-add__end ">
                                            <div class="form-group has-feedback">
                                                <label id="emirates-img">{{trans('frontend.p_passport_back')}}</label>
                                                <div class="avatar-upload">
                                                    <div class="avatar-edit">
                                                        <input type='file' class="upload_file pbid" data-file-name="file4"
                                                            name="trader_images[passport_back]" id="imageUpload3"
                                                            accept=".png, .jpg, .jpeg" />
                                                        {{-- <label for="imageUpload3"></label> --}}
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div class="file4" onclick='$("#imageUpload3").click()'>
                                                        </div>
                                                        <label for="imageUpload3" class="avatar-editt edit_file4" style="display:none;"><i
                                                                class="fa fa-pencil"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="documets-add__group">
                                        <div class="documets-add__end  ">
                                            <div class="form-group has-feedback">
                                                <label id="emirates-img">{{trans('frontend.p_other_doc')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" upload-wrap uo-wrap">
                                        <div class="documets-add__group">
                                    
                                        <div class="documets-add__start add-btn manage-other-file" >
                                            <div class="form-group has-feedback">                                        
                                                <div class="avatar-upload ">
                                                    <div class="avatar-edit">
                                                        <input type='file' class="multi upload-handle" name="trader_images[other_doc][]"
                                                            accept=".png, .jpg, .jpeg" />
                                                    </div>

                                                    <div class="avatar-preview">
                                                        <div class="avatar-preview-click" >
                                                        </div>
                                                        {{--  <label for="imageUpload" class="avatar-editt" style="display:none;"><i class="fa fa-pencil"></i></label>  --}}
                                                        

                                                        <span class="rem avatar-remove" style="display:none;" >
                                                        <a href="javascript:void(0);" style="font-size: 30px;">-</a></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        </div>
                                    </div>
                                
                            </div>


                                <div class="form-log-in__button-group">
                                     {{-- <input type="submit" class="btn btn-primary" style=" " id="send-data" value="Submit" />  --}}
                                    <button type="submit" id="send-data" class="btn btn-transparent">{{trans('frontend.registration')}}</button>
                                    <a href="{{ url(session()->get('language').'/login')}}" class="btn btn-transparent signBtn-open">{{trans('frontend.login')}}</a>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        <script src="{{URL::asset('css/frontend/assets/js/main.min.js')}}"></script>

    <script>

        var uploadedFiles = [];
        $(document).ready(function () {          
            $(".number").keypress(function (e) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    $("#errmsg").html("Digits Only").show().fadeOut("slow");
                    return false;
                }
            });
   
        });
        
        function getUniqueID() {
            return (Math.random() * 100000).toFixed();
        }
        
      
              
        // single file upload function 
        function readURL(input, action_id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.' + action_id).css('background-image', 'url(' + e.target.result + ')');
                    $('.' + action_id).hide();
                    $('.' + action_id).fadeIn(650);     
        
                  
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        jQuery(document).on('change', '.upload_file', function (e) {
            var action_id = $(this).attr('data-file-name');
            
            /*
            if(action_id == 'file1'){
                localStorage.removeItem('efid');
            }
            else if(action_id == 'file2') {
                localStorage.removeItem('ebid');
            }
            else if(action_id == 'file3') {
                localStorage.removeItem('pfid');
            }
            else if(action_id == 'file4') {
                localStorage.removeItem('pbid');
            }
            */
          
        
            readURL(this, action_id);   
            $('.edit_'+action_id).show();
           
        });
        
           
        
        
        $(document).on('click', '.avatar-remove', function() {
        
            $(this).parent().parent().parent().parent().remove();
        
            var imageCount = $('.manage-other-file').length;
            if (imageCount >= 3) {
                   // $(this).parent().parent().parent().parent().parent().prepend(addImages);
                $('.add-btn').show();
            }
        
        });
        $(document).on('click', '.avatar-preview-click', function() {
        //$('.avatar-preview-click').on('click', function (e) {
        
            $(this).parent().parent().find('.upload-handle').click();
        });
        
        
        
        $(function () {
        // Multiple images preview in browser
            var imagesPreview = function (input, placeToInsertImagePreview) {
        
                if (input.files && input.files[0]) {
        
                    var filesAmount = input.files.length;
                    
                    for (i = 0; i < filesAmount; i++) {
                        var reader = new FileReader();
        
                        reader.onload = function (event) {
                        
                            var unique_id = 'other_' + getUniqueID();
                            $('<div class="' + unique_id + '"  style="background-image: url(' +
                                event.target.result + ');">' +
                                '</div>'
        
                            ).appendTo(placeToInsertImagePreview);
        
                            var count = $('.data-limit').length;
                            if (count == 4) {
                                $('.add-btn').hide();
                            }
                        }
        
                        reader.readAsDataURL(input.files[i]);
                    }
                }
        
            };
        
            
        
           // $('.upload-handle').on('change', function () {
             $(document).on('change', '.upload-handle', function() {
                var currentFileHandler = $(this);
                imagesPreview(this, $(this).parent().parent().find('.avatar-preview'));
                $(this).parent().parent().parent().parent().removeClass('add-btn');
                
                $(this).parent().parent().find('.avatar-remove').show();
        
                $(this).parent().parent().find('.avatar-preview-click').hide();
                
                //add more images
                var addImages = '<div class="documets-add__end uo-wrap add-btn manage-other-file" id="">'+'<div class="form-group has-feedback">'+                                       
                        '<div class="avatar-upload ">'+ 
                            '<div class="avatar-edit">'+ 
                                '<input type="file" class="multi upload-handle" name="trader_images[other_doc][]"   accept=".png, .jpg, .jpeg" />'+ 
                                     '</div>'+ 
                                     '<div class="avatar-preview">'+ 
                                        '<div class="avatar-preview-click" >'+ 
                                            '</div>'+  
                                            '<span class="rem avatar-remove"  style="display:none;" >'+
                                              ' <a href="javascript:void(0);" style="font-size: 30px;">-</a></span>'+ 
                                            '</div>'+ 
                                            '</div>'+ 
                                            '</div>'+ 
                                            '</div>';
              
                    var imageCount = $('.manage-other-file').length;
                    $(this).parent().parent().parent().parent().parent().append(addImages);
        
                            if (imageCount == 4) {
                                 
                               $('.add-btn').hide();
                            }
        
        
              
               // $('.uo-wrap-2').show();
            });
        });
        
        
       
        setTimeout(function(){ 
            $('.alert-success,.alert-danger').hide();
        }, 3000);
        
        
              
            </script>
</html>