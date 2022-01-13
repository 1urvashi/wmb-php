<form class="" id="update-sales-types" action="{{ route('update-sales-types', $auction->id) }}" method="post">
     <!-- Modal body -->
     @if(isset($data))
     <input name="_method" type="hidden" value="PUT">
     @endif
    {{ csrf_field() }}
          <div class="form-group col-md-12">
               <label for="">Sales Types</label>
               <select name="sales_type_id" class="form-control" id="sales_type_id">
                    <option value="">Choose Sales Type</option>
                    @foreach ($salesTypes as $item)
                         <option @if(!empty($auction->sale_type_id)) {{ ($auction->sale_type_id == $item->id) ? "selected" : '' }} @endif value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
               </select>
               <div id="sales_type_id_error" class="errorDiv"></div>
          </div>
         <div class="form-group footer-modal" style="margin-left: 15px;">
                   <button type="submit" class="btn btn-primary">Update</button>
                   <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
</form>
