<form class="" id="duplicate" action="{{route('sales.types.duplicate')}}" method="post">
     <!-- Modal body -->
     {{ csrf_field() }}
     <div class="row">
          <div class="col-md-12">
               <label for="name">Name</label>
               <input type="text" name="name" id="name" class="form-control" value="" placeholder="Sale Type Name"
                    autocomplete="off">
               <div id="name_error" class="errorDiv"></div>
          </div>
     </div>
     <input type="hidden" value="{{ $sale_type_id }}" name="type_id">
     <div class="footer-modal" style="margin-top: 15px;">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>
     </div>

</form>