@extends('admin.layouts.master')
@section('content')

@php($user = Auth::guard('admin')->user())
@php($dashviewAction = !is_null($user) && $user && Gate::allows('dashboard_read') ? 1 : 0)
     @include('admin.includes.status-msg')
  <!-- Small boxes (Stat box) -->
               @if($dashviewAction)
                    <div class="row">
                        <div class="col-lg-4 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>{{$data['live']}}</h3>

                                    <p>Active Auctions</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <span class="small-box-footer">&nbsp;</a>
                            </div>
                        </div>

                        <!-- ./col -->
                        {{--<div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>{{$data['liveBranches']}}</h3>

                                    <p>Branches with live auctions</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <span class="small-box-footer">&nbsp;</a>
                                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>--}}
                        <!-- ./col -->
                        <div class="col-lg-4 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>{{$data['sold']}}</h3>

                                    <p>Watches Sold</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <span class="small-box-footer">&nbsp;</a>
                                {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                     <h3>{{$data['cashed']}}</h3>

                                    <p>Watches Cashed</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                               <span class="small-box-footer">&nbsp;</a>
                                {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>


                     <div class="row">

                        <!-- ./col -->
                        <div class="col-lg-4 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>{{$data['carsLive']}}</h3>

                                    <p>Watches - Live</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <span class="small-box-footer">&nbsp;</a>
                                {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                     <h3>{{$data['carsInventory']}}</h3>

                                   <p>Watches - Inventory</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <span class="small-box-footer">&nbsp;</a>
                                {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>{{$data['carsDeals']}}</h3>

                                   <p>Watches - Deals</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                               <span class="small-box-footer">&nbsp;</a>
                                {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>


                    <div class="row">

                      <div class="col-lg-4 col-xs-6">
                          <!-- small box -->
                          <div class="small-box bg-purple">
                              <div class="inner">
                                  <h3>{{$data['closedToday']}}</h3>

                                 <p>Todays - Closed Auctions</p>
                              </div>
                              <div class="icon">
                                  <i class="ion ion-pie-graph"></i>
                              </div>
                             <span class="small-box-footer">&nbsp;</a>
                              <a href="{{url('trader-auction')}}" class="small-box-footer">View more <i class="fa fa-arrow-circle-right"></i></a>
                          </div>
                      </div>

                    </div>
                     @endif
                    <!-- /.row -->
                    <!-- Main row -->
@endsection
