<style>
    /* Navbar Modern di Bawah */
    .navbar {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(255, 255, 255, 0.9);
        /* Latar belakang semi-transparan */
        backdrop-filter: blur(10px);
        /* Efek blur */
        border-top-left-radius: 20px;
        /* Sudut melengkung di atas kiri */
        border-top-right-radius: 20px;
        /* Sudut melengkung di atas kanan */
        box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.1);
        /* Bayangan di atas */
        padding: 10px 20px;
        z-index: 1000;
        /* Pastikan navbar selalu di atas */
    }

    .navbar .container {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Tombol Menu */
    .navbar .btn {
        background: #ff9800;
        /* Warna oranye */
        border: none;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        /* Bentuk bulat */
        display: flex;
        justify-content: center;
        align-items: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .navbar .btn:hover {
        transform: scale(1.1);
        /* Efek membesar saat hover */
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        /* Bayangan saat hover */
    }

    .navbar .btn:active {
        transform: scale(0.9);
        /* Efek mengecil saat diklik */
    }

    .navbar .btn i {
        color: white;
        /* Warna ikon putih */
        font-size: 24px;
        /* Ukuran ikon */
    }

    /* Efek hover pada ikon */
    .navbar .btn:hover i {
        color: #fff;
        /* Warna ikon tetap putih saat hover */
    }
</style>
