@extends('dealer.layouts.master')
@section('content')
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

                                    @if( $auction->status == $auction->getStatusType(7) || $auction->status == $auction->getStatusType(8))
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
                                    @if(!empty($auction->tradersBid->first_name))
                                    {{-- @if ($user->is_verify_email == 1) --}}
                                        
                                    
                                      @if(  $auction->status == $auction->getStatusType(7) || $auction->status == $auction->getStatusType(8)  )
                                      <tr>
                                          <th>Bid Owner</th>
                                          <td>
                                            Name:- <b>{{$auction->tradersBid->first_name}}</b><br>
                                            Email:- <b>{{$auction->tradersBid->email}}</b><br>
                                            Mobile:- <b>{{$auction->tradersBid->phone}}</b>
                                          </td>
                                      </tr>
                                      @endif
                                    @endif
                                    {{-- @endif --}}
                                    <tr>
                                        <th>&nbsp;</th>
                                        <td>
                                            <a href="{{url('dealer/object/detail/'.$auction->object_id)}}">
                                                <button class="btn btn-primary">Watch Details</button>
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
@if($auction->status == $auction->getStatusType(3) || $auction->status == $auction->getStatusType(7) || $auction->status == $auction->getStatusType(8))
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
    @endif


</div>
<div class="row">
<?php /* ?>
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
                            <table id="auction-table" class="table table-striped table-bordered table-hover" role="grid" aria-describedby="auction-table_info">
                                @include('includes.ajax-auction-details',['auction'=>$auction,'bidHistory'=>$bidHistory])
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
<?php */  ?>
@if( $auction->status == $auction->getStatusType(7) || $auction->status == $auction->getStatusType(8))
    @if(!empty($aBids))
    <div class="col-md-6">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Automatic bids</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body no-padding">
                        <div class="col-md-6">
                            <table id="auction-table-automatic-bid" class="table table-striped table-bordered table-hover" role="grid" aria-describedby="auction-table_info">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_disabled" >Trader</th>
                                        <th class="sorting_disabled">Bid Amount</th>
                                </thead>

                                <tbody id="auction-list">
                                    @foreach($aBids as $aBid)
                                        <tr role="row">
                                            <td>{{$aBid['trader_id']}}</td>
                                            <td>{{$aBid['amount']}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

</div>
<script>




</script>
    @endsection

@push('scripts')
<script>
function goBack() {
    window.history.back();
}
</script>
  @endpush
