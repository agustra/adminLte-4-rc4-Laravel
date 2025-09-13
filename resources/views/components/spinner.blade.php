{{-- <style>
    .spinner-border {
        width: 3rem;
        /* Ukuran spinner */
        height: 3rem;
        /* Ukuran spinner */
        border: 0.4em solid rgba(0, 0, 0, 0.1);
        /* Meningkatkan ketebalan border */
        border-radius: 50%;
        /* Membuat spinner berbentuk lingkaran */
        border-left-color: #007bff;
        /* Warna bagian kiri spinner */
        animation: spin 1s linear infinite;
        /* Animasi berputar */
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
            /* Mengatur animasi berputar */
        }
    }
</style> --}}


<div class="spinner-border text-primary d-none" role="status" style="width: 3rem; height: 3rem; border-width: 0.4em;">
    <span class="visually-hidden">Loading...</span>
</div>
