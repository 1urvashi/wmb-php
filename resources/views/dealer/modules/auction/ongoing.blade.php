@extends('dealer.layouts.master')
@section('content')
@include('dealer.includes.status-msg')
@include('includes.ongoing-auction',['all'=>0])
@endsection
@push('scripts')
@endpush
