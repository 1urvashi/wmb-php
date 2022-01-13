@extends('admin.layouts.master')
@section('content')
@include('includes.credit-history',['credits'=>$credits])
@endsection