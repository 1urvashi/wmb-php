@extends('admin.layouts.master')
@section('content')
@php($user = Auth::guard('admin')->user())
@php($bidHistoryAuction = !empty($user) && $user && Gate::allows('Bid-History_read') ? 1 : 0)
@php($bidOwnerView = !empty($user) && $user && Gate::allows('Bid-History_Owner-View') ? 1 : 0)


<div class="row">
    <div class="col-md-6">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Auction Details</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body no-padding">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Title:</th>
                                        <td>{{$auction->title}}</td>
                                    </tr>
                                    <tr>
                                        <th>Start Time</th>
                                        <td>{{$auction->UaeDate($auction->start_time)}}</td>
                                    </tr>
                                    <tr>
                                        <th>End Time</th>
                                        <td>{{$auction->UaeDate($auction->end_time)}}</td>
                                    </tr>


                                    @if(!empty($saleTypeName))
                                    <tr>
                                        <th>SaleType</th>
                                        <td>{{ $saleTypeName }}</td>
                                    </tr>
                                    @endif
                                    @if($auction->status == $auction->getStatusType(3))
                                    <tr>
                                        <th>Last Bid Amount</th>
                                        <td>{{$auction->lastBid()}}</td>
                                    </tr>

                                    <tr>
                                        <th>Last Bid Time</th>
                                        <td>{{$auction->UaeDate($auction->lastBidDate())}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($auction->deducted_amount))
                                    <tr>
                                        <th>Price to selling customer</th>
                                        <td>{{ $auction->deducted_amount }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Min Increment</th>
                                        <td>{{$auction->min_increment}}</td>
                                    </tr>
                                    <tr>
                                        <th>Type</th>
                                        <td>{{$auction->getAuctionType($auction->type)}}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{$auction->status}}</td>
                                    </tr>
                                    @if(!empty($auction->is_negotiated))
                                    <tr>
                                        <th>Negotiated</th>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Negotiated Price</th>
                                        <td>{{(int) $auction->negotiated_amount}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($inspectorNegaotiate))
                                    <tr>
                                        <th>Inspector Negotiated</th>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <th>Inspector Negotiated Amount</th>
                                        <td>{{(int) $inspectorNegaotiate->override_amount}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($auctions->final_req_amount))
                                    <tr>
                                        <th>Negotiate with Bid Owner</th>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <th>Negotiate with Bid Owner</th>
                                        <td>{{(int) $inspectorNegaotiate->final_req_amount}}</td>
                                    </tr>
                                    @endif

                                    @if(!empty($overrideBid))
                                    <tr>
                                        <th>Override Bid</th>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <th>Override Bid Amount</th>
                                        <td>{{(int) $overrideBid->price}}</td>
                                    </tr>
                                    @endif

                                    @php
                                        $dealer = \App\DealerUser::withTrashed()
                                            ->where('id', $auction->dealer_id)->first();

                                    $restoreDealer = \App\DealerUser::where('id', $auction->dealer_id)->restore();
                                    @endphp

                                    <tr>
                                        <th>Dealer Name</th>
                                        <td><a href="{{url('dealers/'.$auction->dealer_id.'/edit')}}" class="btn btn-warning">{{$dealer->name}}</a></td>
                                    </tr>

                                    @if($bidOwnerView)
                                      @if( ($auction->getOriginal('status') >= 3) && (!empty($auction->tradersBid->first_name)) )
                                      <tr>
                                          <th>Bid Owner</th>
                                          <td><a href="{{url('traders/'.$auction->tradersBid->id)}}" class="btn btn-primary">{{$auction->tradersBid->first_name}}</a></td>
                                      </tr>
                                      @endif
                                    @endif
                                    <tr>
                                        <th>&nbsp;</th>
                                        <td>
                                            <a href="{{url('object/detail/'.$auction->object_id)}}">
                                                <button class="btn btn-success">Watch Details</button>
                                            </a>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @can('auction_deduction-details-read')
    @if(!empty($auction->sale_type_id) && !empty($saleType['sales_type_type']))
        @php
            $registered_in = \App\ObjectAttributeValue::where('object_id', $auction->object_id)->where('attribute_id', 20)->first();
            $bankLoan = \App\ObjectAttributeValue::where('object_id', $auction->object_id)->where('attribute_id', 18)->first();
            // $sales_type = 100;
        @endphp
        <div class="col-md-6">
            <div class="box box-success">
                <div class="x_panel">
                    <div class="box-header">
                        <h2 class="box-title">Deduction Details</h2>
                        @if($auction->status == $auction->getStatusType(1))
                        <a data-toggle="modal" data-target="#sales-type-model" data-href="{{ route('get-sales-types', $auction->id) }}" class="btn btn-warning btn-xs text-right pull-right"><i class="fa fa-pencil-square-o"></i> Edit</a>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="box-body no-padding">
                            <div class="col-md-12" id="sales_type_details">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sales Type Name:</th>
                                            <td>{{ $saleType['sales_type_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type</th>
                                            <td>{{ $saleType['sales_type_type'] == 1 ? "Traditional" : "Hybrid" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bid Price</th>
                                            <td><b>{{ $bidPrice }}</b></td>
                                        </tr>

                                        <tr>
                                            <th>Other Amount</th>
                                            <td>{{ $other_amount }}</td>
                                        </tr>

                                        @if($saleType['sales_type_type'] == 1)
                                        <tr>
                                            <th>Margin</th>
                                            <td>{{ $margin_amount }}</td>
                                        </tr>
                                        <tr>
                                            <th>Vat</th>
                                            <td>{{ $vat }}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <th>Margin</th>
                                            <td>{{ $margin_amount }}</td>
                                        </tr>
                                        <tr>
                                            <th>Vat</th>
                                            <td>{{ $vat }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Price to selling customer</th>
                                            <td><b>{{ round($saleType['amount']) }}</b></td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
    <div class="col-md-6">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Deduction Details</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body no-padding">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td colspan="2">No Sales type available</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endcan
</div>

@if($bidHistoryAuction)
<div class="row">
    <div class="col-md-6">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Bid History</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body no-padding">
                        <div class="col-md-12">
                            <table id="auction-table" class="table table-striped table-bordered table-hover" role="grid"
                                aria-describedby="auction-table_info">
                                @include('includes.ajax-auction-details',['auction'=>$auction,'bidHistory'=>$bidHistory])
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-md-6">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Automatic bids</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body no-padding">
                        <div class="col-md-12">
                            <table id="auction-automatic-table" class="table table-striped table-bordered table-hover"
                                role="grid" aria-describedby="auction-automatic-table_info">
                                @include('includes.ajax-automatic-bid-details',['auction'=>$auction,'automaticBidHistory'=>$automaticBidHistory])
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="col-md-6">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Automatic bids</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body no-padding">
                        <div class="col-md-12">

                            <table id="auction-table-automatic-bid" class="table table-striped table-bordered table-hover"
                                role="grid" aria-describedby="auction-table_info">
                                @if(!empty($aBids))
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_disabled">Trader</th>
                                        <th class="sorting_disabled">Bid Amount</th>
                                        <th class="sorting_disabled">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="auction-list">
                                    @foreach($aBids as $aBid)
                                    <tr role="row">
                                        <td>{{$aBid['trader_id']}}</td>
                                        <td>{{$aBid['amount']}}</td>
                                        <td>{{$aBid['updated_at']}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @else
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_disabled" >Trader</th>
                                        <th class="sorting_disabled">Bid Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="auction-list">
                                    <tr role="row">
                                        <td colspan="2" style="text-align: center;">No History data</td>
                                    </tr>
                                </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endif

<!-- The Modal -->
<div class="modal" id="sales-type-model">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Sales Type</h4>
            </div>
            <div class="errorData"></div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>
<div class="loading" style="display:none;">Loading&#8230;</div>
<link rel="stylesheet" href="{{url('css/loader.css')}}">
<style>
    .loading {
        z-index: 9999;
    }
</style>
@endsection

@push('scripts')
<script>
    function goBack() {
        window.history.back();
    }

    $(document).ready(function () {
        @if($auction->status == $auction->getStatusType(1))
            var auction_id = "{{ $auction->id }}"
            var url = "{{ url('auctions/automaticBidAjax/') }}/" + auction_id;
            console.log(url);

            setInterval(function () {
                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "html",
                    success: function (response) {
                        $('#auction-table-automatic-bid').html(response);
                    }
                });
            }, 3000);
        @endif
    });

    $('#sales-type-model').on('show.bs.modal', function (e) {
        $('.loading').show();
        var url = $(e.relatedTarget).attr('data-href');
        $.ajax({
            url: url,
            context: this,
            method: "GET",
            dataType: "html",
            beforeSend: function () {

            },
            success: function (response) {
                $(this).find('.modal-body').html(response);
                $('.loading').hide();
            },
            complete: function () {
                $('.loading').hide();
                //$('.ajax-loading').hide();
            }
        });
    });

    $('#sales-type-model').on('submit', 'form#update-sales-types', function (e) {
    e.preventDefault();
    $('.loading').show();
    var action = $(this).attr('action');
    var _token = $("input[name='_token']").val();
    var name = $("input[name='name']").val();
    var sales_type_id = $("select[name='sales_type_id']").val();
    $.ajax({
        url: action,
        type:'POST',
        data: {_token:_token, sales_type_id:sales_type_id},
        success: function(response) {
            $('#sales_type_details').html(response);
            $('#sales-type-model').modal('hide');
            $('.loading').hide();
        },
        error: function (response) {
                var errors = jQuery.parseJSON(response.responseText);
                $.each(errors, function (key, val) {
                    $("#" + key + '_error').html(val).show();
                });
                $('.loading').hide();
        }
    });
});

</script>
@endpush
