<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="x_panel">
                <div class="box-header">
                    <h2 class="box-title">Auction Details</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="box-body no-padding">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Title:</th>
                                    <td>{{$auction->title}}</td>
                                </tr>
                                <tr>
                                    <th>Start Time</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>End Time</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Bid Amount</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Bid Time</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Min Increment</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Bid Owner</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>&nbsp;</th>
                                    <td>
                                        <a href="{{url('object/detail/'.$auction->object_id')}}">
                                            <button class="btn btn-primary">View</button>
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

<script>
        



</script>  