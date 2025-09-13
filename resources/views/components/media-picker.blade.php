@props([
    'modalId' => 'mediaPickerModal',
    'title' => 'Select Media',
    'mode' => 'picker',
    'acceptedTypes' => ['image/*'],
    'folder' => '',
    'collection' => '',
    'multiSelect' => false,
    'hideUpload' => false,
    'onSelect' => null
])

<!-- Include required CSS -->
@push('css')
    <link rel="stylesheet" href="{{ asset('css/media-upload.css') }}">
    @include('admin.media.partials.styles')
@endpush

<!-- Media Picker Modal -->
@include('admin.media.partials.picker-modal', [
    'modalId' => $modalId,
    'title' => $title,
    'hideUpload' => $hideUpload,
    'hideNavigation' => false,
    'hideToolbar' => false
])

<!-- Include required JS -->
@push('js')
    @include('admin.media.partials.scripts', [
        'mode' => $mode,
        'acceptedTypes' => $acceptedTypes,
        'folder' => $folder,
        'collection' => $collection,
        'multiSelect' => $multiSelect,
        'onSelectCallback' => $onSelect,
        'globalVar' => 'mediaPicker_' . str_replace('-', '_', $modalId)
    ])
@endpush