                 <div class="row">
                            <div class="title-det">
                                <h3>{{trans('frontend.specify')}}</h3>
                            </div>
                        <div class="col-md-12 tabb-d">
                             <div class="tabs">
                                <ul  class="nav nav-pills">
                                    @foreach($attributeSet as $attribute_set)
                                             @php($objects = $auction->getObjectValue($attribute_set->id,$auction->object_id) ? $auction->getObjectValue($attribute_set->id,$auction->object_id) : [])
                                             @if(!$objects->isEmpty())
                                                  <li class="{{$attribute_set->slug}} @if($attribute_set->slug == 'body-work') active @endif"><a  href="#{{$attribute_set->slug}}" data-toggle="tab">{{$attribute_set->name}}</a>
                                                  </li>
                                             @endif
                                    @endforeach
                                </ul>
                            </div>
                            <div class="content tab-content clearfix">
                                @foreach($attributeSet as $attribute_set)
                                     @if($attribute_set->slug == 'body-work')
                                     <div class="tab-pane active" id="{{$attribute_set->slug}}">
                                         <div class="flexslider">
                                            <div class="video_wrapper">
                                                <video id="caranim" width="" height="" autoplay>
                                                    <source src="../../../css/images/car.mp4" type="video/mp4"/>
                                                </video>
                                            </div>
                                            <ul class="slides hidden">
                                                <li class="side_left_view">
                                                    <div class="">
                                                        <div class="front_left {{$auction->getObjectAttribute(1,$auction->object_id) ? $auction->getObjectAttribute(1,$auction->object_id)->color : ''}}"></div>
                                                        <div class="front_left_door {{$auction->getObjectAttribute(36,$auction->object_id) ? $auction->getObjectAttribute(36,$auction->object_id)->color : ''}}"></div>
                                                        <div class="back_left_door {{$auction->getObjectAttribute(38,$auction->object_id) ? $auction->getObjectAttribute(38,$auction->object_id)->color : ''}}"></div>
                                                        <div class="back_left {{$auction->getObjectAttribute(41,$auction->object_id) ? $auction->getObjectAttribute(41,$auction->object_id)->color : ''}}"></div>
                                                    </div>
                                                    <div class="">
                                                        <div class="wheel_left {{$auction->getObjectAttribute(43,$auction->object_id) ? $auction->getObjectAttribute(43,$auction->object_id)->color : ''}}"></div>
                                                        <div class="wheel_left_back {{$auction->getObjectAttribute(120,$auction->object_id) ? $auction->getObjectAttribute(120,$auction->object_id)->color : ''}}"></div>
                                                    </div>

                                                </li>
                                                <li class="front_view">
                                                    <div class="top {{$auction->getObjectAttribute(30,$auction->object_id) ? $auction->getObjectAttribute(30,$auction->object_id)->color : ''}}"></div>
                                                    <div class="bottom {{$auction->getObjectAttribute(29,$auction->object_id) ? $auction->getObjectAttribute(29,$auction->object_id)->color : ''}}"></div>
                                                </li>


                                                <li class="top_view">
                                                    <div class="bonnet {{$auction->getObjectAttribute(30,$auction->object_id) ? $auction->getObjectAttribute(30,$auction->object_id)->color : ''}}"></div>
                                                    <div class="top {{$auction->getObjectAttribute(33,$auction->object_id) ? $auction->getObjectAttribute(33,$auction->object_id)->color : ''}}"></div>
                                                    <div class="trunk_top {{$auction->getObjectAttribute(32,$auction->object_id) ? $auction->getObjectAttribute(32,$auction->object_id)->color : ''}}"></div>
                                                </li>

                                                <li class="side_right_view">
                                                    <div class="">
                                                        <div class="back_right {{$auction->getObjectAttribute(40,$auction->object_id) ? $auction->getObjectAttribute(40,$auction->object_id)->color : ''}}"></div>
                                                        <div class="back_right_door {{$auction->getObjectAttribute(39,$auction->object_id) ? $auction->getObjectAttribute(39,$auction->object_id)->color : ''}}"></div>
                                                        <div class="front_right_door {{$auction->getObjectAttribute(37,$auction->object_id) ? $auction->getObjectAttribute(37,$auction->object_id)->color : ''}}"></div>
                                                        <div class="front_right {{$auction->getObjectAttribute(35,$auction->object_id) ? $auction->getObjectAttribute(35,$auction->object_id)->color : ''}}"></div>
                                                    </div>
                                                    <div class="">

                                                        <div class="wheel_right_back {{$auction->getObjectAttribute(121,$auction->object_id) ? $auction->getObjectAttribute(121,$auction->object_id)->color : ''}}"></div>
                                                        <div class="wheel_right {{$auction->getObjectAttribute(119,$auction->object_id) ? $auction->getObjectAttribute(119,$auction->object_id)->color : ''}}"></div>
                                                    </div>
                                                </li>
                                                <li class="back_view">
                                                    <div class="top {{$auction->getObjectAttribute(32,$auction->object_id) ?$auction->getObjectAttribute(32,$auction->object_id)->color : ''}}"></div>
                                                    <div class="bottom {{$auction->getObjectAttribute(31,$auction->object_id) ?$auction->getObjectAttribute(31,$auction->object_id)->color : ''}}"></div>
                                                </li>
                                            </ul>
                                        </div>
                                     </div>
                                     @elseif($attribute_set->slug == 'options')
                                        <div class="tab-pane" id="{{$attribute_set->slug}}">
                                            @php($objects = $auction->getObjectValue($attribute_set->id,$auction->object_id) ? $auction->getObjectValue($attribute_set->id,$auction->object_id) : [])
                                            @foreach($objects as $object)
                                               @if($object->color == 'green')
                                                <div class="col-md-4 item {{$object->color}}">
                                                    <h4>{{$object->attribute->name}}</h4>
                                                    <p>{{$object->attribute_value}}</p>
                                                </div>
                                               @endif
                                            @endforeach
                                        </div>

                                      @else
                                        <div class="tab-pane" id="{{$attribute_set->slug}}">

                                           	@if($attribute_set->slug == 'car-details')
                                           		<div class="col-md-4 item ">
                                                    <h4>Make</h4>
                                                    <p>{{$make}}</p>
                                                </div>
                                                <div class="col-md-4 item ">
                                                    <h4>Model</h4>
                                                    <p>{{$model}}</p>
                                                </div>
                                           	@endif

                                            @php($objects = $auction->getObjectValue($attribute_set->id,$auction->object_id) ? $auction->getObjectValue($attribute_set->id,$auction->object_id) : [])
                                            @foreach($objects as $object)

                                                <div class="col-md-4 item {{$object->color}}">
                                                    <h4>{{$object->attribute->name}}</h4>
                                                    <p>{{$object->attribute_value}}</p>
                                                </div>
                                            @endforeach
                                        </div>


                                     @endif



                                @endforeach

                                @foreach($attributeSet as $set)
                        <div  class="col-md-12 tabb-d">
                            @if(isset($data[$set->slug]))                        
                            <div class="tabs">
                                <ul class="nav nav-pills">
                                    <li class="car-details active"><a href="#car-details" data-toggle="tab">{{$set->name}}</a>
                                    </li>
                                </ul>
                            </div>
                            @endif
                                @if($set->slug == 'car-details')
                                    <div class="tab-pane active" id="car-details">

                                        <div class="col-md-4 item">
                                        
                                            <h4 style="font-weight: bold;">Brand</h4>
                                            <p>{{$make}}</p>
                                       
                                        </div>
                                    </div>
                                    <div class="tab-pane active" id="car-details">

                                        <div class="col-md-4 item">
                                        
                                            <h4 style="font-weight: bold;">Model</h4>
                                            <p>{{$model}}</p>
                                        
                                        </div>
                                    </div>
                                @endif
                                @if(isset($data[$set->slug]))
                            @foreach($data[$set->slug] as $attrvalue)
                                <div class="tab-pane active" id="car-details">

                                    <div class="col-md-4 item">
                                    
                                        <h4 style="font-weight: bold;">{{$attrvalue->attribute->name}}</h4>
                                        <p>{{$attrvalue->attribute_value}}</p>
                                   
                                    </div>
                                </div>
                            @endforeach
                            @endif
                            </div>
                    @endforeach
                            </div>
                 </div>
            </div>

<script>




</script>
