

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-content border-0 shadow-none" style="border: none; box-shadow: none;">
    <div class="card mb-3 border-0 shadow-none">
        <div class="d-flex justify-content-center align-items-center bg-warning card-img-top" style="height: 200px;">
            <div class="text-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center bg-white shadow"
                    style="width: 100px; height: 100px;">
                    <i class="fas fa-user-lock fa-5x text-warning"></i>
                </div>
                {{-- <h2 class="mt-3">{{ ucfirst($role->name) }}</h2> --}}
            </div>
        </div>


        <div class="card-body">
            <div class="row mb-3">
                <div class="col">
                    <h5> Permission</h5>
                    <span class="description-text">
                        <a href="#" class="nav-link">
                            <span class="badge badge-dark m-1">
                                {{ $permission->name }}
                            </span>
                        </a>
                    </span>
                </div>
                <div class="col">
                    <h5> Role</h5>
                    <span class="description-text">
                        <a href="#" class="nav-link">
                            @if ($permission->getRoleNames()->isEmpty())
                                <span class="badge badge-danger m-1">
                                    Tidak ada role yang ditemukan.
                                </span>
                            @else
                                @foreach ($permission->getRoleNames() as $key => $rol)
                                    <span class="badge badge-dark m-1">
                                        {{ $rol }}
                                    </span>
                                @endforeach
                            @endif
                        </a>
                    </span>
                </div>
            </div>

        </div>
    </div>

    <div class="d-flex justify-content-end" style="padding-right: 1rem; padding-bottom: 1rem;">
        <img src="{{ url('/icons/back.png') }}" alt="Icon" data-bs-dismiss="modal"
            style="width: 30px; height: auto; margin-right: 10px; cursor: pointer; transition: transform 0.2s ease, opacity 0.2s ease;">
    </div>
</div>
