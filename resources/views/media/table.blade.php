@extends('layouts.app')

@section('title', 'Media Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📊 Media Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('media.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-th"></i> Grid View
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-media" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Preview</th>
                                <th>Name</th>
                                <th>File</th>
                                <th>Collection</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Model</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Media Preview Modal -->
<div class="modal fade" id="mediaPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Media Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="media-preview-content"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    @include('media.partials.scripts', [
        'mode' => 'table',
        'globalVar' => 'mediaTable'
    ])
@endsection