<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            {{-- <li class="header">DASHBOARD MANAGEMENT</li> --}}
            @php($user =Auth::guard('admin')->user())

            {{-- @can('permission_read')
            <li class="@if(Request::is('permissions/*') || Request::is('permissions')) {{"active"}} @endif ">
                <a href="{{url('permissions')}}"><i class="fa fa-female"></i> <span>Permissions</span> </a>
            </li>
            @endcan --}}

          {{-- AUCTION MANAGEMENT --}}
          @if(Gate::allows('auction_ongoing') || Gate::allows('auction_closed') || Gate::allows('auction_qualitycheck') || Gate::allows('auction_passcheck') || Gate::allows('auction_failcheck') || Gate::allows('auction_sold') || Gate::allows('auction_cash')
          || Gate::allows('auction_scheduled') || Gate::allows('auction_canceled') || Gate::allows('auction_cancel-closed') || Gate::allows('history_read'))
            <li class="treeview @if(Request::is('auctions/*') || Request::is('history*'))    {{"active"}} @endif ">
              <a href="#"><i class="fa fa-cogs"></i> <span>Auction Management</span> <i class="fa fa-angle-left pull-right"></i></a>

              <ul class="treeview-menu" style="@if( Request::is('auctions/*') || Request::is('history')) {{"display:block"}} @else {{"display:none"}} @endif">
                @can('dashboard_read')
                <li>
                  <a href="{{url('admin')}}"><i class="fa fa-circle-o"></i><span>Dashboard Management</span></a>
                </li>
                @endcan
                @can('auction_ongoing')
                <li @if(Request::is('auctions/ongoing')) {{"class=active"}} @endif >
                  <a href="{{url('auctions/ongoing?xYvrW='.time().rand())}}"><i class="fa fa-circle-o"></i><span>Ongoing Auctions</span></a>
                </li>
                @endcan

                @can('auction_closed')
                <li @if(Request::is('auctions/closed')) {{"class=active"}} @endif >
                  <a href="{{url('auctions/closed')}}"><i class="fa fa-circle-o"></i><span>Closed Auctions</span></a>
                </li>
                @endcan

                @can('auction_cash')
                <li @if(Request::is('auctions/cash')) {{"class=active"}} @endif >
                  <a href="{{url('auctions/cash')}}"><i class="fa fa-circle-o"></i><span>Cash</span></a>
                </li>
                @endcan

                @can('auction_sold')
                <li @if(Request::is('auctions/sold')) {{"class=active"}} @endif >
                  <a href="{{url('auctions/sold')}}"><i class="fa fa-circle-o"></i><span>Sold</span></a>
                </li>
                @endcan



                @can('auction_scheduled')
                <li @if(Request::is('auctions/scheduled')) {{"class=active"}} @endif >
                  <a href="{{url('auctions/scheduled')}}"><i class="fa fa-circle-o"></i><span>Scheduled Auctions</span></a>
                </li>
                @endcan

                @can('auction_canceled')
                <li @if(Request::is('auctions/canceled')) {{"class=active"}} @endif >
                  <a href="{{url('auctions/canceled')}}"><i class="fa fa-circle-o"></i><span>Cancelled Auctions</span></a>
                </li>
                @endcan
                @can('auction_cancel-closed')
                <li @if(Request::is('auctions/cancel-closed')) {{"class=active"}} @endif >
                  <a href="{{url('auctions/cancel-closed')}}"><i class="fa fa-circle-o"></i><span>Cancelled after Completed</span></a>
                </li>
                @endcan
                @can('history_read')
                <li class="@if(Request::is('history/*') || Request::is('history')) {{"active"}} @endif ">
                    <a href="{{url('history')}}"><i class="fa fa-circle-o"></i> <span>History</span> </a>
                </li>
                @endcan
              </ul>
          </li>
          @endif
          {{-- Vehicles  --}}
          @if(Gate::allows('vehicles_read') || Gate::allows('vehicles-under-auction_read') || Gate::allows('make_read') || Gate::allows('model_read'))
          <li class="treeview @if(Request::is('objects/*') || Request::is('make/*') || Request::is('model/*'))    {{"active"}} @endif ">
              <a href="#"><i class="fa fa-cab"></i> <span>Watch Management</span> <i class="fa fa-angle-left pull-right"></i></a>

              <ul class="treeview-menu" style="@if( Request::is('objects/*') || Request::is('make/*') || Request::is('make') || Request::is('model/*') || Request::is('model')) {{"display:block"}} @else {{"display:none"}} @endif">
                  @can('vehicles_read')
                  <li @if(Request::is('objects/noauction')) {{"class=active"}} @endif >
                    <a href="{{url('objects/noauction')}}"><i class="fa fa-circle-o"></i><span>New Watches</span></a>
                  </li>
                  @endcan
                  @can('vehicles-under-auction_read')
                  <li @if(Request::is('objects/auction')) {{"class=active"}} @endif >
                    <a href="{{url('objects/auction')}}"><i class="fa fa-circle-o"></i><span>Watch Under Auctions</span></a>
                  </li>
                  @endcan
                  @can('make_read')
                  <li class="@if(Request::is('make/*') || Request::is('make')) {{"active"}} @endif ">
                      <a href="{{url('make')}}"><i class="fa fa-circle-o"></i> <span>Brand Management</span> </a>
                  </li>
                  @endcan

                  @can('model_read')
                  <li class="@if(Request::is('model/*') || Request::is('model')) {{"active"}} @endif ">
                      <a href="{{url('model')}}"><i class="fa fa-circle-o"></i> <span>Model Management</span> </a>
                  </li>
                  @endcan
              </ul>
          </li>
          @endif

          {{-- USER MANAGEMENT --}}
          @if(Gate::allows('inspectors_read') || Gate::allows('users_read') || Gate::allows('DRM_read') || Gate::allows('customers_read') || Gate::allows('Onboarder_read') || Gate::allows('roles_read') || Gate::allows('branch-managers_read'))
          <li class="treeview @if(Request::is('objects/*') || Request::is('make/*') || Request::is('model/*'))    {{"active"}} @endif ">
              <a href="#"><i class="fa fa-user"></i> <span>User Management</span> <i class="fa fa-angle-left pull-right"></i></a>

              <ul class="treeview-menu" style="@if( Request::is('inspectors/*') || Request::is('inspectors') || Request::is('users/*') || Request::is('users') || Request::is('admin-user/*') || Request::is('admin-user') || Request::is('drmusers/*')
              || Request::is('drmusers') || Request::is('customers/*') || Request::is('customers') || Request::is('onboarder-users/*') || Request::is('onboarder-users') || Request::is('roles/*') || Request::is('roles')
              || Request::is('branch-managers/*') || Request::is('branch-managers')) {{"display:block"}} @else {{"display:none"}} @endif">
              <?php /* ?>
                @can('inspectors_read')
                <li class="@if(Request::is('inspectors/*') || Request::is('inspectors')) {{"active"}} @endif ">
                    <a href="{{url('inspectors')}}"><i class="fa fa-circle-o"></i> <span>Inspectors Management</span> </a>
                </li>
                @endcan
                <?php */ ?>
                @can('users_create')
                <li class="@if(Request::is('users/*') || Request::is('users')) {{"active"}} @endif ">
                    <a href="{{url('users')}}"><i class="fa fa-circle-o"></i> <span>Admin User Management</span> </a>
                </li>
                @endif
                @php($super_admin = \App\User::where('type', config('globalConstants.TYPE.SUPER_ADMIN'))->where('id', $user->id)->first())
                @if(!empty($super_admin))
                <li class="@if(Request::is('admin-user/*') || Request::is('admin-user')) {{"active"}} @endif ">
                  <a href="{{url('admin-user')}}"><i class="fa fa-female"></i> <span>Main Admin Management</span> </a>
                </li>
                @endif
                @can('DRM_read')
                {{--  <li class="@if(Request::is('drmusers/*') || Request::is('drmusers')) {{"active"}} @endif ">
                    <a href="{{url('drmusers')}}"><i class="fa fa-circle-o"></i> <span>DRM Management</span> </a>
                </li>  --}}
                @endcan
                @can('customers_read')
                {{-- <li class="@if(Request::is('customers/*') || Request::is('customers')) {{"active"}} @endif ">
                    <a href="{{url('customers')}}"><i class="fa fa-circle-o"></i> <span>Customers Management</span> </a>
                </li> --}}
                @endcan
                @can('Onboarder_read')
                {{--  <li class="@if(Request::is('onboarder-users/*') || Request::is('onboarder-users')) {{"active"}} @endif ">
                    <a href="{{url('onboarder-users')}}"><i class="fa fa-circle-o"></i> <span>Onboarder Management</span> </a>
                </li>  --}}
                @endcan

                @can('roles_read')
                <li class="@if(Request::is('roles/*') || Request::is('roles')) {{"active"}} @endif ">
                    <a href="{{url('roles')}}"><i class="fa fa-circle-o"></i> <span>Roles</span> </a>
                </li>
                @endcan

                @can('branch-managers_read')
                {{--  <li class="@if(Request::is('branch-managers/*') || Request::is('branch-managers')) {{"active"}} @endif ">
                    <a href="{{url('branch-managers')}}"><i class="fa fa-circle-o"></i> <span>Branch Managers</span> </a>
                </li>  --}}
                @endcan
              </ul>
          </li>
          @endif
          {{-- Trader  --}}
          @if(Gate::allows('traders_read') || Gate::allows('traders-group_read') || Gate::allows('make_read') || Gate::allows('model_read') || $user->type == config('globalConstants.TYPE.SUPER_ADMIN') || $user->type == config('globalConstants.TYPE.DRM') || $user->type == config('globalConstants.TYPE.ADMIN'))
          <li class="treeview @if(Request::is('traders/*') || Request::is('admin/traders-group/*') || Request::is('trader-auction/*')  || Request::is('admin/traders-view-deleted'))    {{"active"}} @endif ">
              <a href="#"><i class="fa fa-users"></i> <span>Traders Management</span> <i class="fa fa-angle-left pull-right"></i></a>

              <ul class="treeview-menu" style="@if( Request::is('traders/*') || Request::is('traders') || Request::is('admin/traders-group/*') || Request::is('admin/traders-group') || Request::is('trader-auction') || Request::is('admin/traders-view-deleted')) {{"display:block"}} @else {{"display:none"}} @endif">
                  @can('traders_read')
                  <li class="@if(Request::is('traders/*') || Request::is('traders')) {{"active"}} @endif ">
                      <a href="{{url('traders')}}"><i class="fa fa-circle-o"></i> <span>Traders</span> </a>
                  </li>
                  @endcan
                  {{--@can('traders-group_read')
                  <li class="treeview @if(Request::is('admin/traders-group*') || Request::is('admin/traders-group'))    {{"active"}} @endif ">
                          <a href="{{url('admin/traders-group')}}"><i class="fa fa-circle-o"></i> <span>Traders Group Management</span> </a>
                  </li>
                  @endcan
                  @if($user->type == config('globalConstants.TYPE.SUPER_ADMIN') || $user->type == config('globalConstants.TYPE.DRM') || $user->type == config('globalConstants.TYPE.ADMIN'))
                  <li class="@if(Request::is('trader-auction/*') || Request::is('trader-auction')) {{"active"}} @endif ">
                      <a href="{{url('trader-auction')}}"><i class="fa fa-circle-o"></i> <span>Trader Auctions</span> </a>
                  </li>
                  @endif--}}

                  @can('traders_View-Deleted')
                  <li class="@if(Request::is('traders-view-deleted/*') || Request::is('admin/traders-view-deleted')) {{"active"}} @endif ">
                      <a href="{{url('admin/traders-view-deleted')}}"><i class="fa fa-circle-o"></i> <span>Deleted Traders</span> </a>
                  </li>
                  @endif
              </ul>
          </li>
          @endif

          {{-- Dealer MANAGEMENT  --}}
          @if(Gate::allows('branches_read'))
          <li class="treeview @if(Request::is('traders/*') || Request::is('admin/traders-group/*') || Request::is('trader-auction/*'))    {{"active"}} @endif ">
              <a href="#"><i class="fa fa-bank"></i> <span>Dealer Management</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu" style="@if( Request::is('dealers/*') || Request::is('dealers')) {{"display:block"}} @else {{"display:none"}} @endif">
                  @can('branches_read')
                  <li class="@if(Request::is('dealers/*') || Request::is('dealers')) {{"active"}} @endif ">
                      <a href="{{url('dealers')}}"><i class="fa fa-circle-o"></i> <span>Dealers</span> </a>
                  </li>
                  @endcan
              </ul>
          </li>
          @endif

          {{-- Utilities MANAGEMENT  --}}
          @if(Gate::allows('priceType_vat-update') || Gate::allows('priceType_salestype-read') || Gate::allows('priceType_vat-update') || Gate::allows('Push-Notification_read'))
          <li class="treeview @if(Request::is('admin/vat/*') || Request::is('sales-types/*') || Request::is('sales-types') || Request::is('profit-management/*') || Request::is('notifications*') || Request::is('notification-history*') || Request::is('notification-templates*'))    {{"active"}} @endif ">
              <a href="#"><i class="fa fa-credit-card"></i> <span>Utilities</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu" style="@if(Request::is('admin/vat/*') || Request::is('sales-types/*') || Request::is('sales-types') || Request::is('profit-management/*') || Request::is('notifications*') || Request::is('notification-history*') || Request::is('notification-templates*')) {{"display:block"}} @else {{"display:none"}} @endif">
                  @can('priceType_vat-update')
                  <li @if(Request::is('admin/vat')) {{"class=active"}} @endif >
                    <a href="{{url('admin/vat')}}"><i class="fa fa-circle-o"></i><span>VAT in %</span></a>
                  </li>
                  @endcan
                  <?php /* ?>
                  @can('priceType_salestype-read')
                  <li @if(Request::is('sales-types') || Request::is('profit-management/*') || Request::is('sales-types/*')) {{"class=active"}} @endif >
                    <a href="{{url('sales-types')}}"><i class="fa fa-circle-o"></i><span>Sales Type</span></a>
                  </li>
                  @endcan
                  <?php */ ?>
                  @can('Push-Notification_read')
                  <li class="@if(Request::is('notification-history*') || Request::is('notifications*') || Request::is('notification-templates*')) {{"active"}} @endif ">
                      <a href="{{url('notifications')}}"><i class="fa fa-circle-o"></i> <span>Push Notifications</span> </a>
                  </li>
                  @endcan
              </ul>
          </li>
          @endif

          {{-- SETTINGS  --}}
          @if(Gate::allows('settings_version-control') || Gate::allows('attributeSet_read') || Gate::allows('attribute_read') || Gate::allows('settings_password-change') || Gate::allows('bank_read'))
          <li class="treeview @if(Request::is('admin/version*') || Request::is('attributeset*') || Request::is('attribute*') || Request::is('admin/password*') || Request::is('bank*'))    {{"active"}} @endif ">
              <a href="#"><i class="fa fa-cog"></i> <span>Settings</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu" style="@if(Request::is('admin/version*') || Request::is('attributeset*') || Request::is('attribute*') || Request::is('admin/password*') || Request::is('bank*')) {{"display:block"}} @else {{"display:none"}} @endif">
                  @can('settings_version-control')
                  <li class="treeview @if(Request::is('admin/version*'))    {{"active"}} @endif ">
                      <a href="{{url('admin/version')}}"><i class="fa fa-circle-o"></i> <span>Version</span> </a>
                  </li>
                  @endcan

                  @can('attributeSet_read')
                  <li class="@if(Request::is('attributeset/*') || Request::is('attributeset')) {{"active"}} @endif ">
                      <a href="{{url('attributeset')}}"><i class="fa fa-circle-o"></i> <span>Attribute Set Management</span> </a>
                  </li>
                  @endcan

                  @can('attribute_read')
                  <li class="@if(Request::is('attribute/*') || Request::is('attribute')) {{"active"}} @endif ">
                      <a href="{{url('attribute')}}"><i class="fa fa-circle-o"></i> <span>Attribute Management</span> </a>
                  </li>
                  @endcan
                  @can('settings_password-change')
                  <li class="@if(Request::is('admin/password*') ) {{"active"}} @endif ">
                      <a href="{{url('admin/password/')}}"><i class="fa  fa-circle-o"></i> <span>Change Password</span> </a>
                  </li>
                  @endcan

                  {{--@can('bank_read')
                  <li class="@if(Request::is('bank/*') || Request::is('bank')) {{"active"}} @endif ">
                      <a href="{{url('bank')}}"><i class="fa  fa-circle-o"></i> <span>Bank Management</span> </a>
                  </li>
                  @endcan--}}
              </ul>
          </li>
          @endif

          {{-- CONTACT US   --}}
          @if(Gate::allows('page_terms-read') || Gate::allows('page_faq-read') || Gate::allows('page_about-read') || Gate::allows('page_privacy-read') || Gate::allows('page_contact-read'))
          <li class="treeview @if(Request::is('admin/terms*') || Request::is('admin/faq*') || Request::is('admin/about*') || Request::is('admin/privacy_policy*') || Request::is('admin/contact*'))    {{"active"}} @endif ">
              <a href="#"><i class="fa fa-file"></i> <span>Contact Us</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu" style="@if(Request::is('admin/terms*') || Request::is('admin/faq*') || Request::is('admin/about*') || Request::is('admin/privacy_policy*') || Request::is('admin/contact*')) {{"display:block"}} @else {{"display:none"}} @endif">
                  @can('page_terms-read')
                  <li class="treeview @if(Request::is('admin/terms*'))    {{"active"}} @endif ">
                      <a href="{{url('admin/terms')}}"><i class="fa fa-circle-o"></i> <span>Terms</span> </a>
                  </li>
                  @endcan

                  @can('page_faq-read')
                  <li class="treeview @if(Request::is('admin/faq*'))    {{"active"}} @endif ">
                      <a href="{{url('admin/faq')}}"><i class="fa fa-circle-o"></i> <span>FAQ</span> </a>
                  </li>
                  @endcan

                  @can('page_about-read')
                  <li class="treeview @if(Request::is('admin/about*'))    {{"active"}} @endif ">
                      <a href="{{url('admin/about')}}"><i class="fa fa-circle-o"></i> <span>About Us</span> </a>
                  </li>
                  @endcan

                  @can('page_privacy-read')
                  <li class="treeview @if(Request::is('admin/privacy_policy*'))    {{"active"}} @endif ">
                        <a href="{{url('admin/privacy_policy')}}"><i class="fa fa-circle-o"></i> <span>Privacy Policy</span> </a>
                  </li>
                  @endcan

                  @can('page_contact-read')
                  <li class="treeview @if(Request::is('admin/contact*'))    {{"active"}} @endif ">
                  <a href="{{url('admin/contact')}}"><i class="fa fa-circle-o"></i> <span>Contact</span> </a>
                  </li>
                  @endcan
              </ul>
          </li>
          @endif

          @can('auction_Export-Completed-Auction')
          {{-- <li class="treeview @if(Request::is('download-report/*') || Request::is('download-report'))    {{"active"}} @endif ">
              <a href="{{url('download-report')}}"><i class="fa fa-cloud-download"></i> <span>Download Report</span> </a>
          </li> --}}
          @endif
          @can('audit_report')
          {{-- <li class="treeview @if(Request::is('audit-report/*') || Request::is('audit-report'))    {{"active"}} @endif ">
              <a href="{{url('audit-report')}}"><i class="fa fa-pagelines"></i> <span>Audit Report</span> </a>
          </li> --}}
          @endif



        </ul>
    </section>
</aside>
