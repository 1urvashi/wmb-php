<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header">DASHBOARD MANAGEMENT</li>

            {{--<li class="@if(Request::is('dealer/traders/*') || Request::is('dealer/traders')) {{"active"}} @endif ">
                <a href="{{url('dealer/traders')}}"><i class="fa fa-female"></i> <span>Traders Management</span> </a>
            </li>--}}

            {{--<li class="@if(Request::is('dealer/history/*') || Request::is('dealer/history')) {{"active"}} @endif ">
                <a href="{{url('dealer/history')}}"><i class="fa fa-female"></i> <span>History</span> </a>
            </li>--}}


            <li class="treeview @if(Request::is('dealer/objects/*'))    {{"active"}} @endif ">
                <a href="#"><i class="fa fa-female"></i> <span>Watches Management</span> <i class="fa fa-angle-left pull-right"></i></a>

                <ul class="treeview-menu" style="@if( Request::is('dealer/objects/*')) {{"display:block"}} @else {{"display:none"}} @endif">

                    <li @if(Request::is('dealer/objects/noauction')) {{"class=active"}} @endif ><a href="{{url('dealer/objects/noauction')}}"><i class="fa fa-circle-o"></i><span>New Watches</span></a></li>
                    <li @if(Request::is('dealer/objects/auction')) {{"class=active"}} @endif ><a href="{{url('dealer/objects/auction')}}"><i class="fa fa-circle-o"></i><span>Watches Under Auctions</span></a></li>
                </ul>
            </li>




            <li class="treeview @if(Request::is('dealer/auctions/*'))    {{"active"}} @endif ">
                <a href="#"><i class="fa fa-cogs"></i> <span>Auction Management</span> <i class="fa fa-angle-left pull-right"></i></a>

                <ul class="treeview-menu" style="@if( Request::is('dealer/auctions/*') || Request::is('dealer/history*')) {{"display:block"}} @else {{"display:none"}} @endif">

                    <li @if(Request::is('dealer/auctions/ongoing')) {{"class=active"}} @endif >
                      <a href="{{url('dealer/auctions/ongoing?xYvrW='.time().rand())}}"><i class="fa fa-circle-o"></i><span>Ongoing Auctions</span></a></li>
                    <li @if(Request::is('dealer/auctions/closed')) {{"class=active"}} @endif ><a href="{{url('dealer/auctions/closed')}}"><i class="fa fa-circle-o"></i><span>Closed Auctions</span></a></li>

                      <li @if(Request::is('dealer/auctions/cash')) {{"class=active"}} @endif ><a href="{{url('dealer/auctions/cash')}}"><i class="fa fa-circle-o"></i><span>Cash</span></a></li>
                        <li @if(Request::is('dealer/auctions/sold')) {{"class=active"}} @endif ><a href="{{url('dealer/auctions/sold')}}"><i class="fa fa-circle-o"></i><span>Sold</span></a></li>


                    <li @if(Request::is('dealer/auctions/scheduled')) {{"class=active"}} @endif ><a href="{{url('dealer/auctions/scheduled')}}"><i class="fa fa-circle-o"></i><span>Scheduled Auctions</span></a></li>
                      <li @if(Request::is('dealer/auctions/canceled')) {{"class=active"}} @endif ><a href="{{url('dealer/auctions/canceled')}}"><i class="fa fa-circle-o"></i><span>Cancelled Auctions</span></a></li>
                      <li @if(Request::is('dealer/auctions/cancel-closed')) {{"class=active"}} @endif >
                        <a href="{{url('dealer/auctions/cancel-closed')}}"><i class="fa fa-circle-o"></i><span>Cancelled after Completed</span></a>
                      </li>
                      <li class="@if(Request::is('dealer/history*') || Request::is('history')) {{"active"}} @endif ">
                          <a href="{{url('dealer/history')}}"><i class="fa fa-circle-o"></i> <span>History</span> </a>
                      </li>
                </ul>
            </li>



            <li class="@if(Request::is('user/*') ) {{"active"}} @endif ">
                <a href="{{url('dealer/password/')}}"><i class="fa fa-user-times"></i> <span>Change Password</span> </a>
            </li>
            <li class="@if(Request::is('dealer/get-dealer-profile*') ) {{"active"}} @endif ">
                <a href="{{url('dealer/get-dealer-profile/')}}"><i class="fa fa-user"></i> <span>Update Profile</span> </a>
            </li>

        </ul>
    </section>
</aside>
