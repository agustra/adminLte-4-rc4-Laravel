@extends('layouts.app')

@section('css')
    <!-- DataTables -->
    @include('components.css-datatables')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css"
        integrity="sha512-z/90a5SWiu4MWVelb5+ny7sAayYUfMmdXKEAbpj27PfdkamNdyI3hcjxPxkOPbrXoKIm7r9V2mElt5f1OtVhqA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


@endsection

@section('title', 'Users')
@section('content')

    <div class="container">
        <h2>Daftar MAC Address Terdaftar</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User</th>
                    <th>MAC Address</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($whitelist as $mac)
                    <tr>
                        <td>{{ $mac->user->name }}</td>
                        <td>{{ $mac->mac_address }}</td>
                        <td>
                            <form action="{{ route('mac.whitelist.destroy', $mac->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('components.Modal')
@endsection

@section('js')
    @include('components.js-datatables')

    {{-- <script src="{{ asset('js/user/user.js') }}"></script> --}}

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



@endsection
