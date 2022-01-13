<button class="btn btn-success pull-right" id="confirm" data-id="{{ $show->id }}">Confirm</button> &nbsp;
<button class="btn btn-danger pull-right" data-dismiss="modal" style="margin-right: 10px;">Cancel</button>
<div class="box-body">
    <h4>Date Sent: {{ date('d M Y', strtotime($show->created_at)) }}</h4>
    <p><b>Message:</b> {{ $show->body }}</p>
    <p><b>Sent To:</b> {{ $count_sent }} Traders</p>
</div>

<table id="traders-table-lists" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
            </tr>
            
        </thead>
    </table>