@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="text-center mt-5">
    <h1 class="display-4 text-danger">404</h1>
    <p class="lead">عذرًا، الصفحة التي تبحث عنها غير موجودة.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">العودة إلى الصفحة الرئيسية</a>
</div>
@endsection
