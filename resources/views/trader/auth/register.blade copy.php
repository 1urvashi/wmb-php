<!DOCTYPE html>
<html @if(session()->get('language') == 'ar') dir="rtl" lang="ar" @else lang="en" @endif>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <title>WatchMyBid | Login</title>

    <!-- Style -->
    <link rel="stylesheet" href="{{URL::asset('css/font-awesome.min.css')}}">
    @if(session()->get('language') == 'en')
    <link rel="stylesheet" href="{{URL::asset('css/bootstrap.min.css')}}">
    @else
    <link rel="stylesheet" href="{{URL::asset('css/bootstrap-ar.min.css')}}">
    @endif

    <link rel="stylesheet" href="{{URL::asset('css/frontend/normalize.css')}}">

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
    <style>
        .signup .form-group {
            margin-bottom: 10px;
        }

        .signup .form-control-feedback {
            width: 42px;
            height: 42px;
            line-height: 42px;
        }

        .signup .paddingright {
            padding-right: 5px;
        }

        .signup .paddingleft {
            padding-left: 5px;
        }

        @media(max-width:991px) {
            .signup .paddingright {
                padding-right: 15px !important;
                padding-left: 15px !important;
            }

            .signup .paddingleft {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }
        }
    </style>
</head>

<body class="signin">
    @include('trader.includes.status-msg')
    <section id="signin" class="signup">
        <div class="container">
            <div class="dd-flex">
                <div class="col-sm-5 left">
                    <h1><img src="{{URL::asset('img/logo.svg?ver=1.1')}}" alt="Whatch My Bid"></h1>
                </div>
                <div class="col-sm-7 right">
                    <h2 style=" margin-bottom: 0px;">{{trans('frontend.registration')}}

                        <a style=" color: #fff;" href="{{ url(session()->get('language').'/login')}}" class=""> <i
                                class="fa fa-arrow-left" style="float:right" aria-hidden="true"> Back</i></a>
                    </h2>


                    <form role="form" enctype="multipart/form-data"
                        action="{{url(session()->get('language').'/trader/register')}}" method="post" id="signupForm">
                        {{ csrf_field() }}
                        {{-- <input type="hidden" name="language" value="en" /> --}}
                        <div class="box-body">
                            <div class="row" >
                                <div class="col-sm-6 paddingright">
                                    <div class="form-group has-feedback">
                                        <input type="text" name="first_name" class="form-control"
                                            placeholder="First name" style="" value="{{ old('first_name') }}" />
                                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 paddingleft">
                                    <div class="form-group has-feedback">
                                        <input type="text" name="last_name" class="form-control" placeholder="Last name"
                                            value="{{ old('last_name') }}" style="" />
                                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <input type="email" name="email" class="form-control" placeholder="Email"
                                            value="{{ old('email') }}" style="" />
                                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Password" id="password" value="{{ old('password') }}"
                                            style="" />
                                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                    </div>
                                </div>
                                {{--
                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Retype password" style="" />
                                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                            </div>
                        </div>
                        --}}
                                <div class="col-md-6 paddingright">
                                    <div class="form-group has-feedback">
                                        <input type="text" name="phone" class="form-control number" placeholder="Phone"
                                            value="{{ old('phone') }}" style="" />
                                        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                        {{-- <span id="errmsg" style="color: red;"></span> --}}
                                    </div>
                                </div>
                                <div class="col-md-6 paddingleft">
                                    <div class="form-group has-feedback">
                                        <input type="text" name="estimated_amount" class="form-control number"
                                            placeholder="Estimated Amount" value="{{ old('estimated_amount') }}"
                                            style="" />
                                        <span class="glyphicon glyphicon-number form-control-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6 up-wrap ">
                                    <div class="form-group has-feedback">
                                        <label id="emirates-img">EmiratesId Front</label>
                                        <div class="avatar-upload">
                                            <div class="avatar-edit">
                                                <input type='file' class="upload_file" data-file-name="file1"
                                                    name="trader_images[emirates_id_front]"
                                                    value="{{ old('trader_images') }}" id="imageUpload"
                                                    accept=".png, .jpg, .jpeg" />
                                                {{-- <label for="imageUpload"></label> --}}
                                            </div>

                                            <div class="avatar-preview">
                                                <div class="file1" onclick='$("#imageUpload").click()'>

                                                </div>
                                                <label for="imageUpload" class="avatar-editt"><i
                                                        class="fa fa-pencil"></i></label>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6 uo-wrap ">
                                    <div class="form-group has-feedback">
                                        <label id="emirates-img">EmiratesId Back</label>
                                        <div class="avatar-upload">
                                            <div class="avatar-edit">
                                                <input type='file' class="upload_file" data-file-name="file2"
                                                    name="trader_images[emirates_id_back]" id="imageUpload1"
                                                    accept=".png, .jpg, .jpeg" />
                                                {{-- <label for="imageUpload1"></label> --}}
                                            </div>
                                            <div class="avatar-preview">
                                                <div class="file2" onclick='$("#imageUpload1").click()'>
                                                </div>
                                                <label for="imageUpload1" class="avatar-editt"><i
                                                        class="fa fa-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6 uo-wrap ">
                                    <div class="form-group has-feedback">
                                        <label id="emirates-img">Passport Front</label>
                                        <div class="avatar-upload">
                                            <div class="avatar-edit">
                                                <input type='file' class="upload_file" data-file-name="file3"
                                                    name="trader_images[passport_front]" id="imageUpload2"
                                                    accept=".png, .jpg, .jpeg" />
                                                {{-- <label for="imageUpload2"></label> --}}
                                            </div>
                                            <div class="avatar-preview">
                                                <div class="file3" onclick='$("#imageUpload2").click()'>
                                                </div>
                                                <label for="imageUpload2" class="avatar-editt"><i
                                                        class="fa fa-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6 uo-wrap ">
                                    <div class="form-group has-feedback">
                                        <label id="emirates-img">Passport Back</label>
                                        <div class="avatar-upload">
                                            <div class="avatar-edit">
                                                <input type='file' class="upload_file" data-file-name="file4"
                                                    name="trader_images[passport_back]" id="imageUpload3"
                                                    accept=".png, .jpg, .jpeg" />
                                                {{-- <label for="imageUpload3"></label> --}}
                                            </div>
                                            <div class="avatar-preview">
                                                <div class="file4" onclick='$("#imageUpload3").click()'>
                                                </div>
                                                <label for="imageUpload3" class="avatar-editt"><i
                                                        class="fa fa-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-md-12  ">
                                    <div class="form-group has-feedback">
                                         <label id="emirates-img">Others</label>
                                    </div>
                                </div>
                    {{--  <label id="emirates-img">Others</label>  --}}
                                <div class="col-xs-6 col-md-6 uo-wrap add-btn" id="manage-other-file">
                                    <div class="form-group has-feedback">
                                        {{--  <label id="emirates-img">Others</label>  --}}
                                        
                                        <div class="avatar-upload">
                                            <div class="avatar-edit">
                                                <input type='file' class="upload_file multi" name="trader_images[other_doc][]" id="imageUpload4"
                                                     accept=".png, .jpg, .jpeg" />
                                                
                                                {{--  <input type='file' class="multi_upload_file " name="trader_images[multi_other_doc][]" value="" />   --}}

                                            </div>

                                            <div class="avatar-preview">
                                                <div onclick='$("#imageUpload4").click()'>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <span id="errmsg" style="color: red;"></span> --}}



                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" style=" " id="send-data" value="Submit" />
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
    @if(isset($terms))
    <div id="my_popup">
        <span class="my_popup_close">X</span>
        <div class="data">
            <h4>{{$terms->title}}</h4>
            {!! $terms->body !!}
        </div>
    </div>
    @endif

    <script src="{{URL::asset('js/jquery-3.1.1.min.js')}}"></script>

    <script src="{{URL::asset('js/bootstrap.min.js')}}"></script>
    <script src="{{URL::asset('js/jquery.validate.min.js')}}"></script>
    <script src="https://cdn.rawgit.com/vast-engineering/jquery-popup-overlay/1.7.13/jquery.popupoverlay.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesnt work if you view the page via file:// -->

    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script>
        var uploadedFiles = [];
        $(document).ready(function () {

          
            //called when key is pressed in textbox
            $(".number").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    //display error message
                    $("#errmsg").html("Digits Only").show().fadeOut("slow");
                    return false;
                }
            });
        });



        function getUniqueID() {
            return (Math.random() * 100000).toFixed();
        }



        $('.box-body').on('click', '.rem', function () {
            var remove_class_name = $(this).attr('data-remove-class');
            $('.rem_' + remove_class_name).remove();

            var count = $('.data-limit').length;

            if (count >= 3) {
                $('.add-btn').show();
            }
        });

      

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

        $(function () {


            // Multiple images preview in browser
            var imagesPreview = function (input, placeToInsertImagePreview) {

                console.log(input);
                if (input.files) {
                   debugger;
                    uploadedFiles = uploadedFiles.concat(input.files[0]);
                    console.log('before',uploadedFiles);
                  //$('#imageUpload4').append("trader_images[other_doc][]",uploadedFiles);
                   // $('.multi_upload_file').val(uploadedFiles);

                    var filesAmount = input.files.length;
                    
                    for (i = 0; i < filesAmount; i++) {
                        var reader = new FileReader();

                        reader.onload = function (event) {

                            var image_url = '../img/plus.png';
                            var other_image_name = "trader_images[other_doc][]";
                            var unique_id = 'other_' + getUniqueID();
                            $('<div class="col-xs-6 col-md-6 up-wrap data-limit rem_' + unique_id +
                                '">' +
                                '<div class="form-group has-feedback">' +
                                '<label id="emirates-img"></label>'+    
                                '<div class="avatar-upload ">' +
                                '<div class="avatar-edit">' +
                                '<input type="file" class="upload_file multi" data-file-name="' +
                                unique_id + '"  name=' + other_image_name + '  id="' + unique_id +
                                '" accept=".png, .jpg, .jpeg" />' +

                                '</div>' +
                                '<div class="avatar-preview">' +
                                '<div class="' + unique_id + '"  style="background-image: url(' +
                                event.target.result + ');">' +
                                '</div>' +
                                '<label for="' + unique_id +
                                '" class="avatar-editt"><i class="fa fa-pencil"></i</label>' +
                                '<span class="rem avatar-remove" data-remove-class=' + unique_id +
                                ' ><a href="javascript:void(0);" >-</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>'

                            ).insertBefore(placeToInsertImagePreview);

                            var count = $('.data-limit').length;
                            if (count == 4) {
                                $('.add-btn').hide();
                            }
                        }

                        reader.readAsDataURL(input.files[i]);
                    }
                }

            };

            $('.upload_file').on('change', function () {
                
                imagesPreview(this, 'div#manage-other-file');
                $('.uo-wrap-2').show();
            });
        });
  console.log('after',uploadedFiles);

        $('document').ready(function () {
            jQuery(document).on('click', '.add', function (e) {
                //$(".add").click(function() {
                //  $('<div><input class="files" name="user_files[]" type="file" ><span class="rem" ><a href="javascript:void(0);" >Remove</span></div>').appendTo("#manage-other-file");
                var count = $('.data-limit').length;

                if (count <= 5) {
                    var image_url = '../img/plus.png';
                    var other_image_name = "trader_images[other_doc][]";
                    var unique_id = 'other_' + getUniqueID();
                    $('<div class="col-md-6 data-limit rem_' + unique_id + '">' +
                        '<div class="form-group has-feedback">' +
                        '<div class="avatar-upload ">' +
                        '<div class="avatar-edit">' +
                        '<input type="file" class="upload_file" data-file-name="' + unique_id +
                        '" name=' + other_image_name + '  id="' + unique_id +
                        '" accept=".png, .jpg, .jpeg" />' +
                        '<label for="' + unique_id + '"></label>' +
                        '</div>' +
                        '<div class="avatar-preview">' +
                        '<div class="' + unique_id + '" style="background-image: url(' + image_url +
                        ');">' +
                        '</div>' +
                        '<span class="rem" data-remove-class=' + unique_id +
                        ' ><a href="javascript:void(0);" >Remove</span>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>'
                    ).appendTo("#manage-other-file");
                } else {
                    alert("You can only upload a maximum of 6 files");
                    // $("#errmsg").html("You can only upload a maximum of 6 files").show().fadeOut("slow");
                    return false;
                }

            });
            jQuery(document).on('change', '.upload_file', function (e) {
                var action_id = $(this).attr('data-file-name');
                console.log(this);
                readURL(this, action_id);
            });
        });


    $("#send-data").click(function() {
                var fd = new FormData();
                var files = $('#file')[0].files[0];
                fd.append('file', files);
       
                $.ajax({
                    url: 'upload.php',
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if(response != 0){
                           alert('file uploaded');
                        }
                        else{
                            alert('file not uploaded');
                        }
                    },
                });
            });
      
    </script>
</body>

</html>