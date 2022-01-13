@extends('webviews.layouts.master')
@section('title', '')
@section('content')
<section id="page">
    <div class="page-in">
		<div class="webview-wrapper">
		      		@if(!empty($title))
		      			<h2 style="color:#333">{{$title}}</h2>
		            @endif
		           {!! $content !!}
		</div>
	</div>
</section>
@endsection
