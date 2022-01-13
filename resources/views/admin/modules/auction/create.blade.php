@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">@if(!isset($auction))Create @else Resubmit @endif Auction for - <b>@if(isset($auction)){{$auction->objectList->name}}
                        ({{$auction->objectList->code}})@else{{$object->name}} ({{$object->code}})@endif</b>
                </h3>
                @php($user = Auth::guard('admin')->user())
                @if($user && !is_null($user) && Gate::allows('vehicles_update'))
                <h4 style="text-align:right;"><a href="{{url('object/edit/'. $object->id )}}" class="btn btn-xs btn-success"><i
                            class="fa fa-pencil-square-o"></i> Edit Watch</a></h4>
                @endif
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            @include('admin.includes.status-msg')
            <form role="form" enctype="multipart/form-data" method="post" action="@if(isset($auction)){{url('auctions/'.$auction->id)}}@else{{url('auctions')}}@endif"
                action="{{url('auction/create')}}">
                @if(isset($auction)) <input name="_method" type="hidden" value="PUT"> @endif
                {{ csrf_field() }}
                <div class="box-body">
                    @if(!isset($auction)) <input type="hidden" class="form-control" name="object_id" value="@if(isset($auction)){{$auction->objectList->id}}@else{{$object->id}}@endif">
                    @endif
                    <div class="form-group">
                        <label>Auction Title <span class="req">*</span></label>
                        @if(!empty($parentAuction->title))
                        <input type="text" class="form-control" id="title" name="title" value="@if(isset($parentAuction)){{$parentAuction->title}}@else{{old('title')}}@endif">
                        @else
                        <input type="text" class="form-control" id="title" name="title" value="@if(isset($auction)){{$auction->title}}@else{{old('title')}}@endif">
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Start Price <span class="req">*</span>
                            <br>
                            @if(!empty($object->suggested_amount)) Bid Start price:- {{$object->suggested_amount}}
                            @endif</label>
                        <input type="text" class="form-control" id="start_price" name="base_price" value="@if(isset($auction)){{$auction->base_price}}@else{{old('base_price')}}@endif"
                            onkeypress="return isFieldNumber(event)">
                    </div>
                    <div class="form-group">
                        <label>Minimum Increment <span class="req">*</span></label>
                        <input type="text" class="form-control" name="min_increment" value="@if(isset($auction)){{$auction->min_increment}}@else{{old('min_increment')}}@endif"
                            onkeypress="return isFieldNumber(event)">
                    </div>
                    <div class="form-group">
                        <label>Auction Type <span class="req">*</span></label>
                        <select name="type" class="form-control select2" id="auctionType">
                            <option value="">Choose Auction type</option>
                            @foreach($types as $key=>$type)
                            <option @if(isset($auction) && $auction->type == $key) selected @elseif(old('type') ==
                                $key) selected @endif value="{{$key}}">{{$type}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="hidden" name="sale_type_id" value="5">
                    </div>
                    <div class="form-group">
                        <label>Other Amount</label>
                        <input type="text" name="other_amount" class="form-control" value="@if(isset($auction)){{$auction->other_amount}}@else{{old('other_amount')}}@endif">
                    </div>

                    <div class="form-group" id="buy_section" style="display:none;">
                        <label>Buy Now Price</label>
                        <input type="text" class="form-control" id="buy_price" name="buy_price" value="@if(isset($auction)){{$auction->buy_price}}@else{{old('buy_price')}}@endif"
                            onkeypress="return isFieldNumber(event)">
                    </div>

                    {{-- @if(!isset($auction))--}}
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input id="immediate" name="immediate" type="checkbox" value="1"> Immediate Start
                            </label>
                        </div>
                    </div>
                    {{--@endif--}}
                    <div class="form-group" id="start_time">
                        <label>Start Time <span class="req">*</span></label>
                        <div class="input-group date form_datetime col-md-12" data-date-format="MM d, yyyy HH:ii p"
                            data-link-field="dtp_input1">
                            <input class="form-control" size="16" name="start_time" id="start_time_field" type="text"
                                value="@if(isset($auction)){{$auction->start_time}}@else{{old('start_time')}}@endif"
                                autocomplete="off">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="checkbox">
                                <label for="formin-5">
                                    <input id="formin-5" class="formin" name="formin" type="radio" value="5"> For 5 Min
                                </label>
                                <label for="formin-10">
                                    <input id="formin-10" class="formin" name="formin" type="radio" value="10"> For 10
                                    Min
                                </label>
                                <label for="formin-15">
                                    <input id="formin-15" class="formin" name="formin" type="radio" value="15"> For 15
                                    Min
                                </label>
                                <label for="formin-0">
                                    <input id="formin-0" class="formin" name="formin" type="radio" value="0" checked="checked">
                                    None
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group end_time_wrapper" id="end_time_wrapper">
                        <label>End Time <span class="req">*</span></label>
                        <div class="input-group date form_datetime col-md-12" data-date-format="MM d, yyyy HH:ii p"
                            data-link-field="dtp_input1">
                            <input class="form-control" size="16" id="end_time" name="end_time" type="text" value="@if(isset($auction)){{$auction->end_time}}@else{{old('end_time')}}@endif"
                                autocomplete="off">
                            <span class="input-group-addon timeDate"><span class="glyphicon glyphicon-remove"></span></span>
                            <span class="input-group-addon timeDate"><span class="glyphicon glyphicon-th"></span></span>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(function () {
        // $('#start_time').datetimepicker({  minDate:new Date()});
    });

    $('.form_datetime').datetimepicker({
        format: "dd MM yyyy HH:ii P",
        showMeridian: true,
        autoclose: true,
        todayBtn: false
    });
    $('#immediate').click(function () {
        //console.log(1);
        if ($('#immediate').is(':checked')) {
            $('#start_time').hide();
        } else {
            $('#start_time').show()
        }
    })
    $('.formin').click(function () {
        var startTime = $('#start_time_field').val();
        if ($('#formin-5').is(':checked') || $('#formin-10').is(':checked') || $('#formin-15').is(':checked')) {
            $('#end_time_wrapper').hide();
        } else {
            $('#end_time_wrapper').show();
        }
    })


    var auctionType = $('#auctionType').val();
    checkAuction(auctionType);

    $('#auctionType').change(function (e) {
        var auctionType = $(this, ':selected').val();

        checkAuction(auctionType);
    });

    function isFieldNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    function checkAuction(type) {
        if (type == 5001 || type == 5002) {
            $('#buy_section').show();
            $("#formin-5").prop("checked", false);
            $("#formin-10").prop("checked", false);
            $("#formin-15").prop("checked", false);
            $("#formin-0").prop("checked", true);
            $('#end_time_wrapper').show();
        } else {
            $('#buy_section').hide();
            $("#formin-5").prop("checked", true);
            $("#formin-10").prop("checked", true);
            $("#formin-15").prop("checked", true);
            $("#formin-0").prop("checked", false);
            $('#end_time_wrapper').hide();
        }
    }

    // $(document).on('click','.enable .btn',function(){
    //      var startTime = $('#start_time_field').val();
    //      if(startTime) {
    //           $('#end_time').removeAttr('disabled');
    //           $('.end_time_wrapper .timeDate').show();
    //           $('.end_time_wrapper .enable').hide();
    //      } else {
    //           alert('Please fill the Start Time');
    //           return false;
    //      }
    // })

    $('form').submit(function () {
        $('form button').attr('disabled', true);
    })
</script>
@endpush
