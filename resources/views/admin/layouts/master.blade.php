<!DOCTYPE html>
<html>

<head>
     @include('includes.head')
</head>
@php($user = Auth::guard('admin')->user())
@php($notificationViewAction = !is_null($user) && $user && Gate::allows('notification_read') ? 1 : 0)
<body class="hold-transition skin-blue sidebar-mini">
     <div class="wrapper">

          @include('admin.includes.header')

          <!-- Left side column. contains the logo and sidebar -->
          @include('admin.includes.sidebar')

          <!-- Content Wrapper. Contains page content -->
          <div class="content-wrapper">
               <!-- Content Header (Page header) -->

               <!-- Main content -->
               <section class="content">


                    <!--MAIN CONTENT GOES HERE -->


                    @yield('content')




                    <!--MAIN CONTENT GOES HERE -->

                    <!-- /.row (main row) -->
               </section>
               <!-- /.content -->
          </div>
          <!-- /.content-wrapper -->
          <footer class="main-footer">
               <div class="pull-right hidden-xs">
                    <b>Version</b> 1.0.0
               </div>
               <strong>Copyright &copy; {{date('Y', time())}} <a href="#">WatchMyBid</a>.</strong> All rights reserved.
          </footer>

          <!-- Control Sidebar -->
          <aside class="control-sidebar control-sidebar-dark">
               <!-- Create the tabs -->
               <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
                    <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
                    <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
               </ul>
               <!-- Tab panes -->
               <div class="tab-content">
                    <!-- Home tab content -->
                    <div class="tab-pane" id="control-sidebar-home-tab">
                         <h3 class="control-sidebar-heading">Recent Activity</h3>
                         <ul class="control-sidebar-menu">
                              <li>
                                   <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                                        <p>Will be 23 on April 24th</p>
                                    </div>
                                </a>
                              </li>
                              <li>
                                   <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-user bg-yellow"></i>

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                                        <p>New phone +1(800)555-1234</p>
                                    </div>
                                </a>
                              </li>
                              <li>
                                   <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                                        <p>nora@example.com</p>
                                    </div>
                                </a>
                              </li>
                              <li>
                                   <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-file-code-o bg-green"></i>

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                                        <p>Execution time 5 seconds</p>
                                    </div>
                                </a>
                              </li>
                         </ul>
                         <!-- /.control-sidebar-menu -->

                         <h3 class="control-sidebar-heading">Tasks Progress</h3>
                         <ul class="control-sidebar-menu">
                              <li>
                                   <a href="javascript:void(0)">
                                        <h4 class="control-sidebar-subheading">
                                        Custom Template Design
                                        <span class="label label-danger pull-right">70%</span>
                                    </h4>

                                        <div class="progress progress-xxs">
                                             <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                                        </div>
                                   </a>
                              </li>
                              <li>
                                   <a href="javascript:void(0)">
                                        <h4 class="control-sidebar-subheading">
                                        Update Resume
                                        <span class="label label-success pull-right">95%</span>
                                    </h4>

                                        <div class="progress progress-xxs">
                                             <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                                        </div>
                                   </a>
                              </li>
                              <li>
                                   <a href="javascript:void(0)">
                                        <h4 class="control-sidebar-subheading">
                                        Laravel Integration
                                        <span class="label label-warning pull-right">50%</span>
                                    </h4>

                                        <div class="progress progress-xxs">
                                             <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                                        </div>
                                   </a>
                              </li>
                              <li>
                                   <a href="javascript:void(0)">
                                        <h4 class="control-sidebar-subheading">
                                        Back End Framework
                                        <span class="label label-primary pull-right">68%</span>
                                    </h4>

                                        <div class="progress progress-xxs">
                                             <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                                        </div>
                                   </a>
                              </li>
                         </ul>
                         <!-- /.control-sidebar-menu -->

                    </div>
                    <!-- /.tab-pane -->
                    <!-- Stats tab content -->
                    <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
                    <!-- /.tab-pane -->
                    <!-- Settings tab content -->
                    <div class="tab-pane" id="control-sidebar-settings-tab">
                         <form method="post">
                              <h3 class="control-sidebar-heading">General Settings</h3>

                              <div class="form-group">
                                   <label class="control-sidebar-subheading">
                                    Report panel usage
                                    <input type="checkbox" class="pull-right" checked>
                                </label>

                                   <p>
                                        Some information about this general settings option
                                   </p>
                              </div>
                              <!-- /.form-group -->

                              <div class="form-group">
                                   <label class="control-sidebar-subheading">
                                    Allow mail redirect
                                    <input type="checkbox" class="pull-right" checked>
                                </label>

                                   <p>
                                        Other sets of options are available
                                   </p>
                              </div>
                              <!-- /.form-group -->

                              <div class="form-group">
                                   <label class="control-sidebar-subheading">
                                    Expose author name in posts
                                    <input type="checkbox" class="pull-right" checked>
                                </label>

                                   <p>
                                        Allow the user to show his name in blog posts
                                   </p>
                              </div>
                              <!-- /.form-group -->

                              <h3 class="control-sidebar-heading">Chat Settings</h3>

                              <div class="form-group">
                                   <label class="control-sidebar-subheading">
                                    Show me as online
                                    <input type="checkbox" class="pull-right" checked>
                                </label>
                              </div>
                              <!-- /.form-group -->

                              <div class="form-group">
                                   <label class="control-sidebar-subheading">
                                    Turn off notifications
                                    <input type="checkbox" class="pull-right">
                                </label>
                              </div>
                              <!-- /.form-group -->

                              <div class="form-group">
                                   <label class="control-sidebar-subheading">
                                    Delete chat history
                                    <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                                </label>
                              </div>
                              <!-- /.form-group -->
                         </form>
                    </div>
                    <!-- /.tab-pane -->
               </div>
          </aside>
          <!-- /.control-sidebar -->
          <!-- Add the sidebar's background. This div must be placed
                 immediately after the control sidebar -->
          <div class="control-sidebar-bg"></div>
     </div>
     <!-- ./wrapper -->
     <style media="screen">
          #success-alert {
               position: fixed;
               top: 40px;
               right: 0;
               display: none;
               width: 330px;
          }
          #admin_notification2 a {
               color: #000 !important;
          }
     </style>
     @include('includes.footer')
     @if($notificationViewAction)

     <script type="text/javascript">
          $(document).ready(function() {

            {{--
               @can('notification_read')
               var url = "{{ route('getNewVehicle') }}";
               setInterval(function() {
                    $.ajax({
                         type: "GET",
                         url: url,
                         dataType: "html",
                         success:function(response) {
                              result = JSON.parse(response);
                              notification = result.notifications;
                              count = result.count;
                              console.log("Count "+count);
                              // console.log(notification.length);
                              if(notification.length > 0) {
                                   $('#count').removeClass('label-success');
                                   $('#count').addClass('label-danger');
                              } else {
                                   $('#count').addClass('label-success');
                                   $('#count').removeClass('label-danger');
                              }
                              $('.count_data').html(notification.length);
                              $('#notification_list').empty();

                              $.each(notification, function(index, value){
                                   $('#notification_list').append('<li><a data-id="'+value.id+'"  class="notification_data" href="{{ url('objects/noauction') }}?inspector_sources='+value.source_id+'"><i class="fa fa-car text-aqua"></i> ' + value.messages + '</a></li>');
                                   if(count >  0) {
                                        $("#success-alert").slideDown(500);
                                   }
                              });
                         }
                    });
                    $('#success-alert').slideUp(500);
               }, 5000);



               var url2 = "{{ route('getOtherNewVehicle') }}";
               console.log(url2);

               setInterval(function() {
                    $.ajax({
                         type: "GET",
                         url: url2,
                         dataType: "html",
                         success:function(response) {
                              result = JSON.parse(response);
                              notification = result.notifications1;
                              count = result.count1;
                              console.log("Count "+count);
                              // console.log(notification.length);
                              if(notification.length > 0) {
                                   $('#count2').removeClass('label-success');
                                   $('#count2').addClass('label-danger');
                              } else {
                                   $('#count2').addClass('label-success');
                                   $('#count2').removeClass('label-danger');
                              }
                              $('.count_data2').html(notification.length);
                              $('#notification_list2').empty();

                              $.each(notification, function(index, value){
                                   $('#notification_list2').append('<li><a data-id="'+value.id+'"  class="notification_data2" href="{{ url('objects/noauction') }}?inspector_sources='+value.source_id+'"><i class="fa fa-car text-aqua"></i> ' + value.messages + '</a></li>');
                                   if(count >  0) {
                                        $("#success-alert").slideDown(500);
                                   }
                              });
                         }
                    });
                    $('#success-alert').slideUp(500);
               }, 5000);
               @endcan
               --}}
               $(document).on('click', '.notification_data', function (event) {
                    var id = $(this).data('id');
                    var url = "{{url('notification/status')}}/"+id;
                    console.log(url);
                    if(id) {
                         $.ajax({
                              url: url,
                             type: "GET",
                             dataType: "html",
                             success:function(data) {
                             }
                         });
                         }
               });

               $(document).on('click', '.notification_data2', function (event) {
                    var id = $(this).data('id');
                    var url = "{{url('notification-other/status')}}/"+id;
                    console.log(url);
                    if(id) {
                         $.ajax({
                              url: url,
                             type: "GET",
                             dataType: "html",
                             success:function(data) {
                             }
                         });
                         }
               });

               $(document).on('click', '.closeBtn', function (event) {
                    $("#success-alert").slideDown(500);
               });
          });

          setTimeout(function(){ 
               $('.alert-success,.alert-danger').hide();
          }, 5000);
     </script>
     @endif
     @stack('scripts')
     @yield('scripts')
</body>

</html>
