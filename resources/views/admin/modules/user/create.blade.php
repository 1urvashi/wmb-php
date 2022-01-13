@extends('admin.layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">

                    <h3 class="box-title">User @if(isset($user))<b>Edit {{$user->name}}</b>@endif</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="@if(isset($user)) {{url('users/'.$user->id)}} @else {{url('users')}} @endif">
                    @if(isset($user)) <input name="_method" type="hidden" value="PUT"> @endif
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label>Name <span class="req">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="@if(isset($user)){{$user->name}}@else{{old('name')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Email <span class="req">*</span></label>
                            <input type="text" class="form-control" id="email" name="email" value="@if(isset($user)){{$user->email}}@else{{old('email')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Password @if(!isset($user))<span class="req">*</span>@endif</label>
                            <input type="password" class="form-control" id="email" name="password" >
                        </div>
                        <?php /*
                        @if(!isset($user))
                        <div class="form-group">
                            <label>User Role <span class="req">*</span></label>
                             <select name="role" class="form-control select2">
                               {{--<option @if(isset($user) && $user->role== 1) selected @elseif(old('type') == 1) selected @endif  value="1">Inspector</option>--}}
                               {{-- <option @if(isset($user) && $user->role == 2) selected @elseif(old('type') == 2) selected @endif  value="2">Supervisor</option>--}}
                               <option @if(isset($user) && $user->role == 3) selected @elseif(old('type') == 3) selected @endif  value="3">Auction Controller</option>
                              <?php
                                   $authUser = Auth::guard('admin')->user();
                                   $actionAllowed = !is_null($authUser) && $authUser && $authUser->isAllowed('branchCreation') ? 1 : 0;
                                   if($actionAllowed){
                              ?>
                               {{--<option @if(isset($user) && $user->role == 4) selected @elseif(old('type') == 4) selected @endif  value="4">Branch manager</option>--}}
                          <?php } ?>


                               <option @if(isset($user) && $user->role == 5) selected @elseif(old('type') == 5) selected @endif  value="5">Quality control</option>
                               {{--<option @if(isset($user) && $user->role == 6) selected @elseif(old('type') == 6) selected @endif  value="6">Senior manger</option>
                               <option @if(isset($user) && $user->role == 7) selected @elseif(old('type') == 7) selected @endif  value="7">Head of branches</option>--}}
                               <option @if(isset($user) && $user->role == 8) selected @elseif(old('type') == 8) selected @endif  value="8">Document controller</option>
                               <option @if(isset($user) && $user->role == 10) selected @elseif(old('type') == 10) selected @endif  value="10">Head Of DRM</option>
                             </select>
                         </div>
                         @endif
                         */?>
                         <div class="form-group">
                              <label for="">User Role<span class="req">*</span></label>
                              <select class="form-control select2" name="role">
                                   <option value="">Select a Role</option>
                                   @foreach($roles as $role)
                                   <option @if(isset($selected_role)) @if($selected_role->role_id == $role->id) selected @endif @endif value="{{$role->id}}">{{$role->label}}</option>
                                   @endforeach
                              </select>
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
