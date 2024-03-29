@extends('admin.layouts.master')
@section('content')
<div class="clearfix"></div>
    <div class="col-md-12 box box-success">
        @include('admin.includes.status-msg')
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <img id="mob_display" class="prof-img" style="max-width: 300px;" src="{{$trader->image}}"/>
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
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <h3>Passport</h3>
                        <img class="img-responsive img-thumbnail prof-doc-img"  src="{{$trader->passport}}"/>
                    </div>
                    <div class="col-md-4">
                        <h3>Trade License</h3>
                        <img class="img-responsive img-thumbnail prof-doc-img"  src="{{$trader->trade_license}}"/>
                    </div>
                    <div class="col-md-4">
                        <h3>Document</h3>
                        <img class="img-responsive img-thumbnail prof-doc-img" src="{{$trader->document}}"/>
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
