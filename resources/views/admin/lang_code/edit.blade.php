@extends('base')

@section('title', $title)

@section('content')

<div class="text-end"> 
    <a href="{{ route('admin.lang_codes.index') }}" class="btn btn-sm btn-primary">{{ __('Lang codes') }}</a>
</div>

{!! form($form) !!}

@endsection