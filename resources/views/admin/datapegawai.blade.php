@extends('layouts.admin.app')

@section('title', 'Data Pegawai')

@section('content')
    <!-- Page Heading -->
    <h1 class="h4 mb-4 text-gray-800 font-weight-bold">DATA PEGAWAI</h1>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <form action="{{ route('data.pegawai') }}" method="GET">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-md-auto mb-2 mb-md-0">
                                            <a href="#" class="btn btn-primary btn-block btn-md-auto" id="btnTambahPegawai">
                                                <i class="fa fa-plus"></i> Tambahkan Data
                                            </a>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3 ml-md-auto">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="nama_pegawai" id="nama_pegawai" placeholder="Nama Pegawai" value="{{ request('nama_pegawai') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-primary" type="submit">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th class="d-none d-md-table-cell">No</th>
                                                <th>NIK</th>
                                                <th>Nama Lengkap</th>
                                                <th class="d-none d-md-table-cell">Jabatan</th>
                                                <th class="d-none d-lg-table-cell">No HP</th>
                                                <th class="d-none d-lg-table-cell">Foto</th>
                                                <th class="d-none d-md-table-cell">Email</th>
                                                <th class="d-none d-lg-table-cell">Role</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pegawai as $item)
                                                <tr>
                                                    <td class="d-none d-md-table-cell">{{ $loop->iteration + $pegawai->firstItem() - 1 }}</td>
                                                    <td>{{ $item->nik }}</td>
                                                    <td>{{ $item->nama_lengkap }}</td>
                                                    <td class="d-none d-md-table-cell">{{ $item->jabatan }}</td>
                                                    <td class="d-none d-lg-table-cell">{{ $item->no_hp ?? '-' }}</td>
                                                    <td class="d-none d-lg-table-cell">
                                                        <img src="{{ $item->foto }}" alt="Foto Profil" class="img-fluid rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ $item->email }}</td>
                                                    <td class="d-none d-lg-table-cell">{{ ucfirst($item->role) }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="#" class="editDataPegawai btn btn-primary btn-sm mr-1" data-nik="{{ $item->nik }}"><i class="fa fa-edit"></i></a>
                                                            <form action="{{ route('datapegawai.delete', $item->nik) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="deleteDataPegawai btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Tidak ada data pegawai</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{ $pegawai->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="modal-inputpegawai" tabindex="-1" role="dialog" aria-labelledby="modalTambahPegawaiTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTambahPegawaiTitle">Tambah Data Pegawai</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('datapegawai.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 px-4">
                                <!-- NIK -->
                                <div class="input-group input-group-sm mt-2">
                                    <span class="input-group-text"><i class="fas fa-id-card text-primary"></i></span>
                                    <input type="text" class="form-control rounded @error('nik') is-invalid @enderror" id="nik" name="nik" placeholder="NIK" value="{{ old('nik') }}" required>
                                </div>
                                <!-- Nama Lengkap -->
                                <div class="input-group input-group-sm mt-4">
                                    <span class="input-group-text"><i class="fas fa-user text-primary"></i></span>
                                    <input type="text" class="form-control rounded @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" value="{{ old('nama_lengkap') }}" required>
                                </div>
                                <!-- Jabatan -->
                                <div class="input-group input-group-sm mt-4">
                                    <span class="input-group-text"><i class="fas fa-briefcase text-primary"></i></span>
                                    <input type="text" class="form-control rounded @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" placeholder="Jabatan" value="{{ old('jabatan') }}" required>
                                </div>
                                <!-- No HP -->
                                <div class="input-group input-group-sm mt-4">
                                    <span class="input-group-text"><i class="fas fa-phone text-primary"></i></span>
                                    <input type="tel" class="form-control rounded @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" placeholder="Nomor HP" value="{{ old('no_hp') }}">
                                </div>
                                <!-- Email -->
                                <div class="input-group input-group-sm mt-4">
                                    <span class="input-group-text"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" class="form-control rounded @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                                </div>
                                <!-- Password -->
                                <div class="input-group input-group-sm mt-4">
                                    <span class="input-group-text"><i class="fas fa-lock text-primary"></i></span>
                                    <input type="password" class="form-control rounded @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                                </div>
                                <!-- Role -->
                                <div class="input-group input-group-sm mt-4">
                                    <span class="input-group-text"><i class="fas fa-user-tag text-primary"></i></span>
                                    <select class="form-control rounded" id="role" name="role" required>
                                        <option value="" hidden>Pilih Role</option>
                                        <option value="pegawai">Pegawai</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <!-- Foto -->
                                <div class="input-group input-group-sm my-4">
                                    <span class="input-group-text"><i class="fas fa-image text-primary"></i></span>
                                    <input type="file" class="form-control rounded @error('foto') is-invalid @enderror" id="foto" name="foto">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Kembali</button>
                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Data -->
    <div class="modal fade" id="modal-editpegawai" tabindex="-1" role="dialog" aria-labelledby="modalEditPegawaiTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditPegawaiTitle">Edit Data Pegawai</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadeditform">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            // Tampilkan SweetAlert untuk pesan sukses atau error
            @if (session('success'))
                Swal.fire({
                    title: 'Mantap!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'Oke',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    title: 'Ups!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'Oke',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            @endif

            // Tampilkan SweetAlert jika ada error validasi
            @if ($errors->any())
                Swal.fire({
                    title: 'Ups, Ada Masalah!',
                    html: '{!! implode("<br>", $errors->all()) !!}',
                    icon: 'error',
                    confirmButtonText: 'Oke',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        popup: 'text-center'
                    }
                });
            @endif


            // Tombol tambah pegawai
            $("#btnTambahPegawai").click(function() {
                $("#modal-inputpegawai").modal("show");
            });

            // Edit pegawai via AJAX
            $(".editDataPegawai").click(function() {
                var nik = $(this).data('nik');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('datapegawai.edit') }}',
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        nik: nik
                    },
                    success: function(response) {
                        $("#loadeditform").html(response);
                        $("#modal-editpegawai").modal("show");
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Ups!',
                            text: 'Gagal memuat data, coba lagi nanti ya!',
                            icon: 'error',
                            confirmButtonText: 'Oke',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                });
            });

            // Konfirmasi hapus dengan SweetAlert
            $(".deleteDataPegawai").click(function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Yakin nih?',
                    text: 'Data ini bakal lenyap selamanya lho!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus aja!',
                    cancelButtonText: 'Batal',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger mr-2',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush