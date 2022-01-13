
<div class="row">
    <div class="col-md-12">
        <div class="row">
             <div class="col-md-3">
                  <h3 class="box-title" style="font-weight: bold;">WATCH DETAILS</h3>
                 <h4><b>Title:-</b> {{$object->name}}</h4>
                 <h4><b>Code:-</b> {{$object->code}}</h4>
                 <h4><b>Variation:-</b> {{$object->variation}}</h4>
                 {{--  <h4><b>VIN Number:-</b> {{$object->vin}}</h4>
                 <h4><b>Registration Number:-</b> {{$object->vehicle_registration_number}}</h4>  --}}
                 <h4><b>Dealer Name:-</b> @if($object->DealerDetails){{$object->DealerDetails->name}}@endif</h4>
                 <h4><b>Inspector Name:-</b> @if($object->inspectorDetails){{$object->inspectorDetails->name}}@endif</h4>
           </div>
           @php($user = Auth::guard('admin')->user())
           @php($customersDetailAuction = !empty($user) && $user && Gate::allows('customersDetail') ? 1 : 0)
           @if($customersDetailAuction)
           <div class="col-md-9">
                @if(!empty($object->customer_name))
                    <h3 style="font-weight: bold;">{{trans('frontend.customer_detail')}}</h3>
                @endif
                @if(!empty($object->customer_name))
                        <h4><b>Name:-</b> {{$object->customer_name}}</h4>
                @endif
                @if(!empty($object->customer_mobile))
                        <h4><b>Mobile:-</b> {{$object->customer_mobile}}</h4>
                @endif
                @if(!empty($object->customer_email))
                        <h4><b>Email:-</b> <a href="mailto:{{$object->customer_email}}">{{$object->customer_email}}</a></h4>
                @endif
                @if(!empty($object->customer_reference))
                        <h4><b>Reference:-</b> {{$object->customer_reference}}</h4>
                @endif

                @if(!empty($object->source_of_enquiry))
                        <h4><b>Source:-</b> {{$object->source_of_enquiry}}</h4>
                @endif

                @if(!empty($object->bank_id))
                        <h4><b>Bank:-</b> {{$object->bank->name}}</h4>
                        <h4><b>Address:-</b> {{$object->bank->address}}</h4>
                @endif
         </div>
         @endif
            <form role="form" id="objectEdit" enctype="multipart/form-data" method="post" action="{{url('object/edit/'.$object->id)}}">
                {{ csrf_field() }}
                <div class="col-md-12 oreder">
                    <h4 style="font-weight: bold;">IMAGES</h4>
                    <div class="col-xs-6 item">
                        <div class="form-group">
                            <label> Images (Maximum file size: 5 MB) </label>
                            <input type="file" multiple class="form-control" id="images" data-max-size="32154"  name="images[]" accept="image/jpeg,image/gif,image/png,image/jpg,image/jpg">
                        </div>

                    </div><br><br><br><br>

                    <ul class="users-list clearfix" id="sortable">
                        @foreach($object->images as $image)
                        <li class="ui-state-default" data-id="{{ $image->id }}" >
                            <img style="min-width: 250px;
    min-height: 170px;" src="{{$image->image}}"
                                 data-darkbox="{{$image->image}}" alt="Watch Image">
                            <a onclick="return confirm('Are you sure you want to delete this image?');" style="margin-top: 10px;" href="{{url('remove-watch-data/images/'.$image->id)}}" class="btn btn-danger">Remove</a>
                        </li>
                        @endforeach
                    </ul>
                    <input type="hidden" name="imageids_arr" id="imgVal" value="">
                </div>


                <div class="col-md-12">
                    <h3 style="font-weight: bold;">{{trans('frontend.specify')}}</h3>
                </div>

                  <div class="row">
                       <div class="col-xs-12">
                            <div class="form-group">
                               <div class="col-xs-6">
                                    <label>Brand</label>
                                    <select class="form-control" name="make" id="make" required>
                                         <option value="">Choose Make</option>
                                         @foreach($makes as $make)
                                            <option @if($object->make_id == $make->id) selected @endif value="{{$make->id}}">{{$make->name}}</option>
                                         @endforeach
                                    </select>
                               </div>
                               <div class="col-xs-6">
                                    <label>Model</label>
                                    <select class="form-control" name="model" id="model" required>
                                         <option value="">Choose Model</option>
                                         @foreach($models as $model)
                                             <option @if($object->model_id == $model->id) selected @endif value="{{$model->id}}">{{$model->name}}</option>
                                         @endforeach
                                    </select>
                               </div>
                           </div>
                       </div>
                  </div>
                @foreach($attributeSet as $set)
                <div class="col-md-12">
                     @if(isset($data[$set->slug]))
                <h4 style="font-weight: bold;text-decoration: underline;text-transform: uppercase;">{{$set->name}}</h4>
                    @foreach($data[$set->slug] as $attrvalue)
                        <div class="tab-pane">
                            <div class="col-md-4 item">
                              <div class="row">
                                  @if($attrvalue->attribute->input_type == $attrvalue->attribute->getInputType(0))
                                  <div class="form-group">
                                      <label>{{$attrvalue->attribute->name}}</label>
                                      <input type="text" class="form-control" name="attributeValue[{{$attrvalue->attribute->id}}]" value='{{$attrvalue->attribute_value}}' />
                                  </div>
                                  @elseif($attrvalue->attribute->input_type == $attrvalue->attribute->getInputType(5))
                                  <div class="form-group">
                                      <label>{{$attrvalue->attribute->name}}</label>
                                      <input type="text" class="form-control" name="attributeValue[{{$attrvalue->attribute->id}}]" value='{{$attrvalue->attribute_value}}'/>
                                  </div>
                                  @elseif($attrvalue->attribute->input_type == $attrvalue->attribute->getInputType(8))
                                  <div class="form-group">
                                      <label>{{$attrvalue->attribute->name}}</label>
                                      <input type="email" class="form-control" name="attributeValue[{{$attrvalue->attribute->id}}]" value='{{$attrvalue->attribute_value}}'/>
                                  </div>
                                  @elseif($attrvalue->attribute->input_type == $attrvalue->attribute->getInputType(9))
                                  <div class="form-group">
                                      <label>{{$attrvalue->attribute->name}}</label>
                                      <input type="number" class="form-control" name="attributeValue[{{$attrvalue->attribute->id}}]" value='{{$attrvalue->attribute_value}}'/>
                                  </div>
                                  @elseif(($attrvalue->attribute->input_type == $attrvalue->attribute->getInputType(3)) ||
                                  ($attrvalue->attribute->input_type == $attrvalue->attribute->getInputType(2)) ||
                                  ($attrvalue->attribute->input_type == $attrvalue->attribute->getInputType(1)) ||
                                  ($attrvalue->attribute->input_type == $attrvalue->attribute->getInputType(11))
                                  )
                                  <div class="form-group">
                                       <label>{{$attrvalue->attribute->name}}</label>
                                       <select name="attributeValue[{{$attrvalue->attribute->id}}]" class="form-control" required>
                                           @foreach($attrvalue->attribute->attributeValues as $value)
                                               <option color="{{$value->color}}" @if($value->attribute_value == $attrvalue->attribute_value) selected @endif value="{{$value->attribute_value}}">{{$value->attribute_value}}</option>
                                           @endforeach
                                       </select>
                                   </div>
                                   @else
                                   <div class="form-group">
                                       <label>{{$attrvalue->attribute->name}}</label>
                                       <input type="text" class="form-control" name="attributeValue[{{$attrvalue->attribute->id}}]" value='{{$attrvalue->attribute_value}}'/>
                                   </div>
                                   @endif
                              </div>
                            </div>
                        </div>
                    @endforeach
                    @endif
                    </div>
                    @endforeach

                    <div class="col-md-12  document">
                        <div class="col-xs-6 item">
                            <div class="form-group">
                                <label> Documentation (attachment) (Maximum file size: 5 MB)  </label>
                                <input type="file" multiple class="form-control" id="attachment" data-max-size="32154"  name="attachment[]"  accept="image/jpeg,image/jpg,image/gif,image/png,image/jpg">
                            </div>

                        </div><br><br><br><br>
                        <h4 style="font-weight: bold;">Documentation (attachment)</h4>
                        <ul class="users-list clearfix" id="sortable-docu">
                            @foreach($object->attachments as $attachment)
                            @php($pdfattachment= pathinfo($attachment->attachment, PATHINFO_EXTENSION))
                            
                                <li class="ui-state-default" data-id="{{ $attachment->id }}">   
                                    {{-- @if($pdfattachment == 'pdf')
                                    <a href="{{$attachment->attachment}}" target="_blank"> <img src="{{url('img/pdf-icon.png')}}" alt="" style="min-width: 100px;  min-height: 170px;"></a>

                                    <a onclick="return confirm('Are you sure you want to delete this PDF?');" style="margin-top: 10px;" href="{{url('remove-watch-data/attachment/'.$attachment->id)}}" class="btn btn-danger">Remove</a>

                                    @else --}}
                                    <img style="min-width: 250px; min-height: 170px;" 
                                    src="{{$attachment->attachment}}" data-darkbox="{{$attachment->attachment}}" alt="Watch Image">
                                    <a onclick="return confirm('Are you sure you want to delete this attachment?');" style="margin-top: 10px;" href="{{url('remove-watch-data/attachment/'.$attachment->id)}}" class="btn btn-danger">Remove</a>

                                    {{-- @endif --}}

                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="docids_arr" id="docVal" value="">
                    </div>
                        
                        {{-- <div class="clearfix"></div> --}}
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>

</div>

@push('scripts')
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> --}}
{{-- <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.2/jquery.ui.touch-punch.min.js"></script> --}}
<script>
$('form#objectEdit').submit(function(e){
  e.preventDefault();
  $('form#objectEdit select option').each(function(){
    this.value = this.value+'#'+$(this).attr('color');
  })

  var imageids_arr = [];
  $('#sortable li').each(function(){
       var id = $(this).data('id');
       imageids_arr.push(id);
    });
    $('.oreder').find('#imgVal').val(imageids_arr);

    var docids_arr = [];
    $('#sortable-docu li').each(function(){
        var id = $(this).data('id');
        docids_arr.push(id);
        });
    $('.document').find('#docVal').val(docids_arr);

  $('form#objectEdit')[0].submit();
})

// Initialize sortable
$( "#sortable" ).sortable();
$( "#sortable-docu" ).sortable();


$('#attachment').change(function(){
            var fp = $("#attachment");
            var lg = fp[0].files.length; // get length
            var items = fp[0].files;
            var fileSize = 0;
           
            if (lg > 0) {
                for (var i = 0; i < lg; i++) {
                    fileSize = fileSize+items[i].size; // get file size
                }
                if(fileSize >=	5000000) {
                    alert('File size must not be more than 5 MB');
                    $('#attachment').val('');
                    return false
                }
            }
        });

        $('#images').change(function(){
            var fp = $("#images");
            var lg = fp[0].files.length; // get length
            var items = fp[0].files;
            var fileSize = 0;
           
            if (lg > 0) {
                for (var i = 0; i < lg; i++) {
                    fileSize = fileSize+items[i].size; // get file size
                }
                if(fileSize >=	5000000) {
                    alert('File size must not be more than 5 MB');
                    $('#images').val('');
                    return false
                }
            }
        });
       

$('#make').on('change', function() {
       var makeId = $(this).val();
       $('.loading').show();
       if(makeId) {
            $.ajax({
                url: '{{url("get/models")}}/'+makeId,
                type: "GET",
                dataType: "html",
                success:function(data) {

                    $('#model').empty();

                    $('#model').html(data);
                    $('.loading').hide();


                }
            });
       }else{
            $('#model').empty();
            $('.loading').hide();
       }
    });
</script>
@endpush
