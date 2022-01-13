<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="content clearfix">
                <div class="col-md-3">
                     <h3 class="box-title" style="font-weight: bold;">WATCH DETAILS</h3>
                    <h4><b>Title:-</b> {{$object->name}}</h4>
                    <h4><b>Code:-</b> {{$object->code}}</h4>
                    <h4><b>Branch Name:-</b> {{ !empty(\App\DealerUser::withTrashed()->where('id', $object->dealer_id)->first()->name) ? \App\DealerUser::withTrashed()->where('id', $object->dealer_id)->first()->name : 'Null'}}</h4>

               </div>




                <div class="col-md-12">
                <h4 style="font-weight: bold;">IMAGES</h4>
                    @foreach($object->images as $image)
                   <!-- <img style="max-width: 200px;" src="{{$image->image}}"/>-->
                    <img style="max-height: 200px;" src="{{$image->image}}"
                data-darkbox="{{$image->image}}">

                    @endforeach
                </div>


                <div class="col-md-12">
                    <h3 style="font-weight: bold;">{{trans('frontend.specify')}}</h3>
                </div>
                @foreach($attributeSet as $set)
                <div class="col-md-12">
                     @if(isset($data[$set->slug]))
                <h4 style="font-weight: bold;text-decoration: underline;text-transform: uppercase;">{{$set->name}}</h4>
                @endif
                   		@if($set->slug == 'car-details')
                   			<div class="tab-pane">

								<div class="col-md-4 item">
								<div class="row">
									<h5 style="font-weight: bold;">Make</h5>
									<p>{{$make}}</p>
								</div>
								</div>
							</div>
                  			<div class="tab-pane">

								<div class="col-md-4 item">
								<div class="row">
									<h5 style="font-weight: bold;">Model</h5>
									<p>{{$model}}</p>
								</div>
								</div>
							</div>
                   		@endif
                         @if(isset($data[$set->slug]))
                    @foreach($data[$set->slug] as $attrvalue)
                    @if (!empty($attrvalue->attribute_value))
                        <div class="tab-pane">

                            <div class="col-md-4 item">
                            <div class="row">
                                <h5 style="font-weight: bold;">{{$attrvalue->attribute->name}} @if($attrvalue->additional_text)<i class="fa fa-info-circle" title="{{$attrvalue->additional_text}}" data-toggle="tooltip"></i>@endif</h5>
                                <p>{{$attrvalue->attribute_value}}</p>
                            </div>
                            </div>
                        </div>
                    @endif

                    @endforeach
                    @endif
                    </div>
                    @endforeach

                    <div class="col-md-12">
                         <h4 style="font-weight: bold;">Documentation (attachment)</h4>
                            @foreach($object->attachments as $attachment)
                            {{-- @php($pdfattachment= pathinfo($attachment->attachment, PATHINFO_EXTENSION))
                            @if($pdfattachment == 'pdf')
                            <a href="{{$attachment->attachment}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" width="100%"></a>
                            @else --}}
    
                            <img style="max-height: 200px;" src="{{$attachment->attachment}}"
                            data-darkbox="{{$attachment->attachment}}">
    
                                        {{-- @endif --}}
    
    
                            @endforeach
                     </div>
            </div>
        </div>
    </div>
</div>

<script>




</script>
