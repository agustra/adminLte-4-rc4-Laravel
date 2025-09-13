@extends('layouts.app')

@section('content')
    <div class="container text-center">
        <img src="https://www.hostinger.com.br/tutoriais/wp-content/uploads/sites/12/2017/08/O-que-significa-%E2%80%98erro-403-proibido-%E2%80%93-voce-nao-tem-permissao-de-acesso-nesse-servidor%E2%80%99.webp"
            alt="403 - Akses Ditolak" class="img-fluid">
        <h1 class="text-danger">403 - Akses Ditolak</h1>
        <p class="text-center">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <p class="text-center">Silahkan hubungi admin untuk mengakses halaman ini.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali ke Home</a>
    </div>

    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error!',
                text: 'Anda tidak memiliki izin untuk melakukan aksi ini.',
                icon: 'error',
                confirmButtonText: 'Kembali ke Home',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('dashboard') }}';
                }
            });
        });
    </script> --}}
@endsection
