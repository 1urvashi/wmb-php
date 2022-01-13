@extends('trader.layouts.master',['specialTitle'=>true,'title'=> trans('frontend.terms_menu'),'class'=>'innerpage'])
@section('content')
<section class="section-terms section-about p-10">
  <div class="container">
      <div class="section-about__wrap">
        {!! $content !!}
      </div>
  </div>
</section>
@endsection
