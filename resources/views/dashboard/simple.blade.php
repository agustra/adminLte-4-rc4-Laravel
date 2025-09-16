@extends('layouts.app')

{{-- @section('title', 'Dashboard') --}}

@section('css')
    <!-- ModernTable CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/responsive.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/themes.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js" crossorigin="anonymous"></script> --}}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mb-3">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-user-circle fa-5x text-primary mb-3"></i>
                        </div>
                        <h2 class="mb-3">👋 Selamat datang, {{ $user->name }}</h2>
                        <p class="lead text-muted">🎯 Role: {{ $role }}</p>

                        <hr class="my-4">

                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Bergabung</span>
                                        <span class="info-box-number">{{ $user->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Status</span>
                                        <span class="info-box-number">Aktif</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Login Terakhir</span>
                                        <span class="info-box-number">Sekarang</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Table Demo with External API -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-cart text-primary me-2"></i>Modern Table Demo - External API
                </h5>
                <div class="card-tools">
                    <span class="badge bg-info">DummyJSON API</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <table id="table-modern" class="table table-striped table-hover table-bordered"></table>
                    </div>
                    <div class="col-6">
                        <table id="table-users" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>First name</th>
                                    <th>Last name</th>
                                    <th>email</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script type="module">
        import {
            ModernTable
        } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/core/ModernTable.js";

        const table = new ModernTable('#table-users', {
            api: {
                url: "/api/users",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                        .content,
                },
            },
            columns: [{
                    data: 'id',
                    title: 'ID',
                    width: '60px'
                },
                {
                    data: 'firstName',
                    title: 'First Name'
                },
                {
                    data: 'lastName',
                    title: 'Last Name'
                },
                {
                    data: 'email',
                    title: 'Email'
                },
                {
                    data: 'phone',
                    title: 'Phone'
                }
            ],


            // No serverSide option = client-side processing (like DataTables)
            paging: true,
            pageLength: 10,
            searching: true,
            ordering: true,
            responsive: true
        });

        // Fetch data from DummyJSON API
        // async function fetchProducts() {
        //     try {
        //         const response = await fetch('https://dummyjson.com/products?limit=30');
        //         const data = await response.json();
        //         return data.products; // Return products array
        //     } catch (error) {
        //         console.error('❌ Error fetching products:', error);
        //         return [];
        //     }
        // }

        // Initialize table with fetched data
        // fetchProducts().then(products => {
        //     const table = new ModernTable('#table-products', {
        //         // Use static data from API
        //         data: products,

        //         // Column definitions
        //         columns: [{
        //                 data: 'id',
        //                 title: 'ID',
        //                 width: '60px'
        //             },
        //             {
        //                 data: 'title',
        //                 title: 'Product Name',
        //                 orderable: true
        //             },
        //             {
        //                 data: 'category',
        //                 title: 'Category',
        //                 render: function(data) {
        //                     return `<span class="badge bg-primary">${data}</span>`;
        //                 }
        //             },
        //             {
        //                 data: 'price',
        //                 title: 'Price',
        //                 render: function(data) {
        //                     return `$${parseFloat(data).toFixed(2)}`;
        //                 }
        //             },
        //             {
        //                 data: 'rating',
        //                 title: 'Rating',
        //                 render: function(data) {
        //                     const stars = '⭐'.repeat(Math.floor(data));
        //                     return `<span title="${data}">${stars} ${data.toFixed(1)}</span>`;
        //                 }
        //             },
        //             {
        //                 data: 'stock',
        //                 title: 'Stock',
        //                 render: function(data) {
        //                     const badgeClass = data > 50 ? 'bg-success' : data > 20 ? 'bg-warning' :
        //                         'bg-danger';
        //                     return `<span class="badge ${badgeClass}">${data}</span>`;
        //                 }
        //             }
        //         ],

        //         // Table features
        //         paging: true,
        //         pageLength: 10,
        //         lengthMenu: [5, 10, 25, 50],
        //         searching: true,
        //         columnSearch: true,
        //         ordering: true,
        //         select: true,
        //         responsive: true,

        //         // UI customization
        //         theme: 'auto',
        //         buttons: ['copy', 'csv', 'excel', 'pdf'],

        //         // Advanced features
        //         stateSave: true,
        //         keyboard: true,
        //         accessibility: true,

        //         // Language (Indonesian)
        //         language: {
        //             search: 'Cari produk:',
        //             lengthMenu: 'Tampilkan _MENU_ produk per halaman',
        //             info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ produk',
        //             infoEmpty: 'Tidak ada produk',
        //             infoFiltered: '(difilter dari _MAX_ total produk)',
        //             paginate: {
        //                 first: 'Pertama',
        //                 last: 'Terakhir',
        //                 next: 'Selanjutnya',
        //                 previous: 'Sebelumnya'
        //             },
        //             emptyTable: 'Tidak ada data produk tersedia'
        //         }
        //     });

        //     console.log('✅ Modern Table initialized with', products.length, 'products');
        // });
    </script>
@endsection
