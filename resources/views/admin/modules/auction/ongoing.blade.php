@extends('admin.layouts.master')
@section('content')
@include('admin.includes.status-msg')
@include('includes.ongoing-auction',['all'=>1])
@endsection
@push('scripts')
@endpush
