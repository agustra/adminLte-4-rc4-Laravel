<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
{{-- <script type="module" src="{{ asset('/js/admin/media-library.js') }}"></script> --}}

 {{-- Load axios first --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

{{-- Load media components --}}
@vite(['resources/js/media/index.js'])

{{-- Configuration for MediaLibrary if needed --}}
@if(isset($globalVar) || isset($mode))
<script>
    // Set configuration for MediaLibrary
    window.mediaLibraryConfig = {
        mode: '{{ $mode ?? "full" }}',
        allowUpload: {{ isset($allowUpload) ? ($allowUpload ? 'true' : 'false') : 'true' }},
        allowEdit: {{ isset($allowEdit) ? ($allowEdit ? 'true' : 'false') : 'true' }},
        allowDelete: {{ isset($allowDelete) ? ($allowDelete ? 'true' : 'false') : 'true' }},
        multiSelect: {{ isset($multiSelect) ? ($multiSelect ? 'true' : 'false') : 'true' }},
        acceptedTypes: {!! isset($acceptedTypes) ? json_encode($acceptedTypes) : '["image/*", "video/*", "audio/*", "application/*"]' !!},
        folder: '{{ $folder ?? "" }}',
        collection: '{{ $collection ?? "" }}'
    };
    
    @if(isset($globalVar))
    // Set global variable name
    window.mediaLibraryGlobalVar = '{{ $globalVar }}';
    @endif
</script>
@endif