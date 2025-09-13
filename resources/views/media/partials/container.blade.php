<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $title ?? '📁 Media Library' }}</h3>
                    <div class="card-tools">
                        @if(!isset($hideViewToggle) || !$hideViewToggle)
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm view-toggle active" data-view="grid">
                                <i class="fas fa-th"></i> Grid
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm view-toggle" data-view="list">
                                <i class="fas fa-list"></i> List
                            </button>
                        </div>
                        @endif
                        {{ $headerTools ?? '' }}
                    </div>
                </div>
                <div class="card-body">
                    @if(!isset($hideUpload) || !$hideUpload)
                        @include('media.partials.upload-area')
                    @endif
                    
                    @if(!isset($hideNavigation) || !$hideNavigation)
                        @include('media.partials.navigation')
                    @endif
                    
                    @if(!isset($hideToolbar) || !$hideToolbar)
                        @include('media.partials.toolbar')
                    @endif
                    
                    @include('media.partials.media-grid')
                    
                    {{ $slot ?? '' }}
                </div>
            </div>
        </div>
    </div>
</div>