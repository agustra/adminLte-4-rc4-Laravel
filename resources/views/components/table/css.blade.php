<style>
    thead,
    tfoot,
    tbody tr {
        width: 100%;
        table-layout: fixed;
    }

    /* =========================== */
    /* ===== START INPUT SEARCH =====*/
    /* =========================== */




    /* =========================== */
    /* ===== START INPUT SEARCH =====*/
    /* =========================== */


    /* =========================== */
    /* ===== START CHILD ROW =====*/
    /* =========================== */
    /* Kolom yang tersembunyi di layar kecil */
    .hidden-column {
        display: none;
    }

    .child-row {
        background-color: #f9f9f9;
        /* Soft background color */
        padding: 10px;
        /* Padding inside the child row */
        margin: 10px 0;
        /* Margin between child rows */
    }

    .child-row span {
        padding: 20px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }

    .child-row strong {
        display: inline-block;
        /* Ensure label and value are on the same line */
        width: 150px;
        /* Fixed width for labels */
        font-weight: bold;
        /* Make labels bold */
        padding-bottom: 5px;
        /* Space between label and bottom border */
        margin-bottom: 5px;
        /* Space between labels */
    }

    .child-row button {
        margin-right: 5px;
        /* Space between buttons */
    }

    /* Tampilkan tombol toggle hanya di layar kecil */
    @media (max-width: 768px) {
        .toggle-row {
            display: inline-block;
            /* margin-left: 10px; */
        }

        /* Sembunyikan baris anak secara default */
        .child-row {
            display: none;
            background-color: #f9f9f9;
        }

        /* Sembunyikan kolom dan header saat layar kecil */
        td.hidden-column,
        th.hidden-column {
            display: none;
        }
    }

    /* Default untuk layar lebih besar */
    @media (min-width: 768px) {
        .toggle-row {
            display: none;
        }

        /* Tampilkan kolom tersembunyi di layar besar */
        td.hidden-column,
        th.hidden-column {
            display: table-cell;
        }
    }

    .toggle-row {
        font-size: 12px;
        /* Ukuran font */
        border: 2px solid rgba(248, 249, 250, .5) !important;
        /* Border */
        border-radius: 50%;
        /* Bulat */
        padding: 0;
        /* Hapus padding */
        height: 15px;
        /* Tinggi tombol */
        width: 15px;
        /* Lebar tombol */
        display: flex;
        /* Gunakan flexbox */
        justify-content: center;
        /* Pusatkan teks secara horizontal */
        align-items: center;
        /* Pusatkan teks secara vertikal */
        cursor: pointer;
        /* Ubah kursor saat hover */
        margin: 0;
        /* Hapus margin */
        line-height: 1px;
        /* Sesuaikan line-height */
        padding-bottom: 2px;
    }

    /* =========================== */
    /* ===== END CHILD ROW =====*/
    /* =========================== */




    /* =========================== */
    /* ===== START PAGINATION =====*/
    /* =========================== */

    .pagination>li>a,
    .pagination>li>span {
        /* Properti lainnya tetap sama */
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        border-radius: 50% !important;
        height: 30px !important;
        width: 30px !important;
        color: #4285f4 !important;
        margin: 0 2px !important;
        padding: 5px 0 !important;
        /* Menambahkan padding atas dan bawah untuk menaikkan teks */
        border: 1px solid #e0e0e0 !important;
        text-decoration: none !important;
        font-size: 12px !important;
    }

    .pagination>li>a>span {
        line-height: 1px;
        padding-bottom: 3px;
    }

    .pagination>li.active>a {
        background-color: #4285f4 !important;
        color: white !important;
    }

    .pagination>li>a:hover {
        background-color: #e0e0e0 !important;
    }

    .rows-per-page {
        max-width: 65px;
        /* Atur lebar maksimum */
        min-width: 65px;
        /* Atur lebar minimum agar tidak terlalu kecil */
    }

    /* =========================== */
    /* ===== END PAGINATION =====*/
    /* =========================== */

    /* =========================== */
    /* ===== UNTUK SORT =====*/
    /* =========================== */

    th {
        cursor: pointer;
        /* Menunjukkan bahwa kolom bisa diklik untuk sort */
        background-color: #f4f4f4;
    }

    .asc::after {
        content: ' ▲';
    }

    .desc::after {
        content: ' ▼';
    }

    /* =========================== */
    /* ===== UNTUK SORT =====*/
    /* =========================== */

    /* =========================== */
    /* ===== UNTUK PROCESSING /  LOADING =====*/
    /* =========================== */
    .tansTack_processing {
        /* display: none; */
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: none;
        background-color: transparent;
        padding: 0;
        width: 100px;
        height: 100px;
        text-align: center;
        line-height: 100px;
        /* background-image: url('{{ asset('img/loading-gif.gif') }}'); */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .tansTack_processing i.fa-spinner {
        font-size: 5rem;
        margin-top: 0;
        animation: spin 2s linear infinite;
    }

    /* add animation keyframes */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* add color gradient effect */
    .tansTack_processing i.fa-spinner {
        color: #FFC107;
        /* updated color */
        background: linear-gradient(to bottom, #FFC107, #FF69B4);
        /* background: linear-gradient(to bottom, #3498db, #f1c40f, #632f53, #d11e48, #f4dd51, #a1c5ab, #fde6bd); */
        /* add gradient effect */
        background-clip: text;
        /* clip the gradient to the text */
        -webkit-background-clip: text;
        /* for webkit browsers */
        -webkit-text-fill-color: transparent;
        /* for webkit browsers */
    }




    /* =========================== */
    /* ===== UNTUK PROCESING /  LOADING =====*/
    /* =========================== */

    /* Seleksi semua elemen dengan data-column="action" */
    /* [data-column="action"] {
        text-align: center;
    } */

    /* Seleksi tombol di dalam kolom action */
    /* [data-column="action"] button {
        background: #007bff;
        color: #fff;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    } */
</style>
