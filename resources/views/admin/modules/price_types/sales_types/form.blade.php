<form class="" id="profitMargin" action="@if(isset($data)){{route('profit-management.update', $data->id)}} @else{{route('profit-management.create')}}@endif" method="post">
     <!-- Modal body -->
     @if(isset($data))
     <input name="_method" type="hidden" value="PUT">
     @endif
    {{ csrf_field() }}
          <div class="form-group">
              <div class="col-sm-12">
                   <label>Add Range</label>
              </div>
              <div class="col-sm-6">
                        <label for="">From</label>
                        <input type="text" name="from" id="from" class="form-control" value="@if(isset($data)){{$data->range_from}}@endif" required placeholder="From" autocomplete="off">
                        <div id="from_error" class="errorDiv"></div>
              </div>
              <div class="col-sm-6">
                        <label for="">To</label>
                        <input type="text" name="to" id="to" class="form-control" value="@if(isset($data)){{$data->range_to}}@endif" required placeholder="To" autocomplete="off">
                         <div id="to_error" class="errorDiv"></div>
              </div>
         </div>
         <div class="form-group selectProfitType">
              <div class="col-sm-12">
                   <label for="">Select Profit Type</label>
                   <div class="select-profit">
                        <div class="row">
                             <div class="col-md-12">
                                  <div class="col-sm-6">
                                       <label for="fixed" class="proft-type">
                                            <input type="radio" name="profit" id="fixed" value="1" @if(isset($data)) {{$data->profit_status == 1 ? 'checked' : ''}} @endif  required />
                                            Fixed Profit
                                       </label>
                                  </div>
                                  <div class="col-sm-6">
                                       <label for="percentage" class="proft-type">
                                            <input type="radio" name="profit" id="percentage" value="2" @if(isset($data)) {{$data->profit_status == 2 ? 'checked' : ''}} @endif   required />
                                            Percentage Profit
                                       </label>
                                  </div>
                             </div>
                        </div>


                        <div class="row">
                             <div class="col-md-12">
                                  <div class="col-sm-6 " style="margin-top: 20px;">
                                       <label for="amount" class="amount-label">Enter Amount</label>
                                            <input type="text" class="form-control" name="amount" id="amount" value="@if(isset($data)){{$data->profit_amount}}@endif" required placeholder="Amount in AED" autocomplete="off">
                                            <div id="amount_error" class="errorDiv"></div>
                                  </div>
                             </div>
                        </div>
                   </div>
              </div>
         </div>
         <div class="form-group footer-modal" style="margin-left: 15px;">
                   <button type="submit" class="btn btn-primary">ADD RANGE</button>
                   <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>
          </div>

</form>
