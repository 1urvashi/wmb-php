<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header">DASHBOARD MANAGEMENT</li>          
            
            <li class="@if(Request::is('user/*') ) {{"active"}} @endif ">
                <a href="{{url('inspector/password/')}}"><i class="fa fa-user-times"></i> <span>Change Password</span> </a>
            </li>
            
        </ul>
    </section>
</aside>