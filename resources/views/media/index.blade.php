@extends('layouts.app')

@section('title', 'Media Library')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/media-upload.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    @include('media.partials.styles')
@endsection

@section('content')
    @include('media.partials.container')

    @include('media.partials.modals')
    @include('media.partials.context-menu')
@endsection

@section('js')
    @include('media.partials.scripts', [
        'mode' => 'full',
        'globalVar' => 'mediaLibrary',
    ])
@endsection
