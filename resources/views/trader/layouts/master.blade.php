<!DOCTYPE html>
<html  @if(session()->get('language') == 'ar') dir="rtl" lang="ar" @else lang="en"  @endif>
  @include('trader.includes.head',['title'=>$title, 'specialTitle'=>(isset($specialTitle) ? $specialTitle : 0)])

  <body class="{{$class}}" >
    @include('trader.includes.header')

    {{-- @include('trader.includes.banner',['title'=>$title, 'specialTitle'=>(isset($specialTitle) ? $specialTitle : 0)]) --}}
  <main role="main">
    @yield('content')
  </main>
    @include('trader.includes.footer')

    @stack('scripts')
    @yield('scripts')
    
  </body>
</html>
