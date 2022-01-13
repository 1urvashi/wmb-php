<!-- jQuery 2.2.0 -->
<script src="{{URL::asset('plugins/jQuery/jQuery-2.2.0.min.js')}}"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  // $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.6 -->
<script src="{{URL::asset('js/bootstrap.min.js')}}"></script>
<script src="{{ asset('plugins/bootstrap-slider/bootstrap-slider.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
{{-- <script src="{{URL::asset('js/jquery-ui.min.js')}}"></script> --}}
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
{{-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"
type="text/javascript"></script> --}}
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="{{URL::asset('plugins/morris/morris.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{URL::asset('plugins/sparkline/jquery.sparkline.min.js')}}"></script>
<!-- jvectormap -->
<script src="{{URL::asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{URL::asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{URL::asset('plugins/knob/jquery.knob.js')}}"></script>
<!-- daterangepicker -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>-->
<script src="{{URL::asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- datepicker -->
<script src="{{URL::asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{URL::asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>
<!-- Slimscroll -->
<script src="{{URL::asset('plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{URL::asset('plugins/fastclick/fastclick.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{URL::asset('js/app.min.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!--<script src="{{URL::asset('js/pages/dashboard.js')}}"></script>-->
<!-- AdminLTE for demo purposes -->
<script src="{{URL::asset('js/jquery.validate.min.js')}}"></script>
<script src="{{URL::asset('js/demo.js')}}"></script>
<script src="{{URL::asset('plugins/datetimepicker/jquery.datetimepicker.js')}}"></script>
<script src="{{URL::asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js')}}"></script>
<script src="{{URL::asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{URL::asset('plugins/multiselect/jquery.bootstrap-duallistbox.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('plugins/datepicker/js/bootstrap-datetimepicker.js')}}" charset="UTF-8"></script>
<script src="{{URL::asset('js/common.js?ver=1.071')}}"></script>
<script src="{{URL::asset('js/script.js')}}"></script>
<script src="{{URL::asset('darkbox/darkbox.js')}}"></script>
<script src="{{URL::asset('js/jquery.countdown.min.js')}}"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <script src="https://www.gstatic.com/firebasejs/3.2.0/firebase.js"></script>
<script>
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});
var config = {
    apiKey: '{{ env('FIREBASE_API_KEY') }}',
    authDomain: '{{ env('FIREBASE_AUTH_DOMAIN') }}',
    databaseURL:'{{ env('FIREBASE_DATABASE_URL') }}',
    projectId:'{{ env('FIREBASE_PROJECT_ID') }}',
    storageBucket:'{{ env('FIREBASE_STORAGE_BUCKET') }}',
    messagingSenderId:'{{ env('FIREBASE_MESSAGE_SENDER_ID') }}'
};

    firebase.initializeApp(config);
    var database = firebase.database();
</script>
