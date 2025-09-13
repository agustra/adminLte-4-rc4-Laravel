<x-app-layout>

    <style>
        img {
            width: 150;
            height: 150;
            border-radius: 50%;
        }

    </style>

    @section('title')
        Edit Profil
    @endsection

    @section('breadcrumb')
        @parent
        <li class="breadcrumb-item active">laporan bulanan</li>
    @endsection

    @section('content')

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <!-- Profile Image -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="row justify-content-center">
                                    <div class="tampil-foto">
                                        <img src="{{ $profil->avatar_url }}" width="200" alt="User profile picture">
                                    </div>
                                </div>

                                <h3 class="profile-username text-center text-bold">
                                    {{ strtoupper(Auth::user()->name) }}</h3>
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Email</b> <a class="float-right">{{ ucfirst(Auth::user()->email) }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Member since</b> <a
                                            class="float-right">{{ Auth::user()->created_at->format('M. Y') }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        @foreach (auth()->user()->getRoleNames() as $item)
                                            <b>Role</b> <a class="float-right">{{ ucfirst($item) }}</a>
                                        @endforeach
                                    </li>
                                </ul>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>

                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">

                                    <li class="nav-item"><a class="nav-link active" href="#settings"
                                            data-toggle="tab">Settings</a>
                                    </li>
                                </ul>
                            </div><!-- /.card-header -->
                            <form action="{{ route('users.update_profil') }}" method="post" class="form-profil"
                                data-toggle="validator" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="tab-content">

                                        <div class="active tab-pane" id="settings">
                                            <form class="form-horizontal">
                                                <div class="form-group row">
                                                    <label for="name" class="col-lg-2 control-label">Nama</label>
                                                    <div class="col-lg-6">
                                                        <input type="text" name="name" class="form-control" id="name"
                                                            required autofocus value="{{ $profil->name }}">
                                                        <span class="help-block with-errors"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="old_password" class="col-lg-2 control-label">Password
                                                        Lama</label>
                                                    <div class="col-lg-6">
                                                        <input type="password" name="old_password" id="old_password"
                                                            class="form-control" minlength="6">
                                                        <span class="help-block with-errors"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="password" class="col-lg-2 control-label">Password</label>
                                                    <div class="col-lg-6">
                                                        <input type="password" name="password" id="password"
                                                            class="form-control" minlength="6">
                                                        <span class="help-block with-errors"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="password_confirmation"
                                                        class="col-lg-2 control-label">Konfirmasi
                                                        Password</label>
                                                    <div class="col-lg-6">
                                                        <input type="password" name="password_confirmation"
                                                            id="password_confirmation" class="form-control"
                                                            data-match="#password">
                                                        <span class="help-block with-errors"></span>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="avatar" class="col-lg-2 control-label">Avatar Profil</label>
                                                    <div class="col-lg-4">
                                                        <input type="file" name="avatar" class="form-control" id="avatar"
                                                            onchange="preview('.tampil-foto', this.files[0])">
                                                        <span class="help-block with-errors"></span>
                                                        <br>
                                                        {{-- <div class="tampil-foto">
                                                            <img src="{{ $profil->avatar_url }}" width="200">
                                                        </div> --}}
                                                    </div>
                                                </div>

                                                <div class="form-group row ">
                                                    <div class="offset-sm-2 col-sm-10 ">
                                                        <button class="btn btn-danger float-right">
                                                            <i class="fa fa-save"></i>
                                                            Simpan Perubahan</button>
                                                        {{-- <button type="submit" class="btn btn-danger">Submit</button> --}}
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div><!-- /.card-body -->
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->


    @endsection

    <script>
        $(function() {
            $('#old_password').on('keyup', function() {
                if ($(this).val() != "") $('#password, #password_confirmation').attr('required', true);
                else $('#password, #password_confirmation').attr('required', false);
            });

            $('.form-profil').validator().on('submit', function(e) {
                if (!e.preventDefault()) {
                    $.ajax({
                            url: $('.form-profil').attr('action'),
                            type: $('.form-profil').attr('method'),
                            data: new FormData($('.form-profil')[0]),
                            async: false,
                            processData: false,
                            contentType: false
                        })
                        .done(response => {
                            $('[name=name]').val(response.name);
                            $('.tampil-foto').html(
                                `<img src="${response.avatar_url}" width="200">`);
                            $('.img-profil').attr('src', response.avatar_url);

                            var Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                            });
                            toastr.success(
                                'Data berhasil di edit Broo...'
                            )
                        })
                        .fail(errors => {
                            if (errors.status == 422) {
                                alert(errors.responseJSON);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops... Ada Yang Salah',
                                    text: 'Tidak Dapat Menyimpan Data Broo!!',
                                })
                            }
                            return;
                        });
                }
            });
        });
    </script>

</x-app-layout>
