@extends('layouts.admin.app')

@section('title', 'Pengajuan Izin')

@section('content')
    <!-- Page Heading -->
    <h1 class="h4 mb-4 text-gray-800 font-weight-bold">PENGAJUAN IZIN</h1>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="text-xs font-weight-bold text-white mb-0">FILTER DATA IZIN</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pengajuan.izin') }}" method="GET" autocomplete="off">
                            <div class="row mb-2">
                                <div class="col-12 col-md-6 mb-2 mb-md-0">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-days"></i></span>
                                        </div>
                                        <input type="date" class="form-control" id="dari" name="dari" value="{{ Request('dari') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-days"></i></span>
                                        </div>
                                        <input type="date" class="form-control" id="sampai" name="sampai" value="{{ Request('sampai') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-3 mb-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK" value="{{ Request('nik') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mb-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama" value="{{ Request('nama_lengkap') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mb-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                        </div>
                                        <select name="status_approved" id="status_approved" class="form-control">
                                            <option value="" hidden>Pilih Status</option>
                                            <option value="Menunggu">Menunggu</option>
                                            <option value="Disetujui">Disetujui</option>
                                            <option value="Ditolak">Ditolak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <button class="btn btn-primary btn-sm btn-block" type="submit">
                                        <i class="fas fa-magnifying-glass mx-2"></i>Cari Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th class="d-none d-md-table-cell">No</th>
                                        <th>Tanggal</th>
                                        <th>NIK</th>
                                        <th>Nama Lengkap</th>
                                        <th class="d-none d-md-table-cell">Jabatan</th>
                                        <th class="d-none d-md-table-cell">Status</th>
                                        <th class="d-none d-md-table-cell">Keterangan</th>
                                        <th>Approve</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($izins as $izin)
                                    <tr>
                                        <td class="d-none d-md-table-cell">{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($izin->tanggal)->format('d-m-Y') }}</td>
                                        <td>{{ $izin->nik }}</td>
                                        <td>{{ $izin->user->nama_lengkap ?? '-' }}</td>
                                        <td class="d-none d-md-table-cell">{{ $izin->user->jabatan ?? '-' }}</td>
                                        <td class="d-none d-md-table-cell">{{ $izin->status }}</td>
                                        <td class="keterangan-col d-none d-md-table-cell" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $izin->keterangan }}">
                                            {{ Str::limit($izin->keterangan, 20, '....') }}
                                        </td>
                                        <td>
                                            <span class="badge w-100 text-light 
                                                @if ($izin->status_approval == 'Menunggu')
                                                    bg-warning
                                                @elseif ($izin->status_approval == 'Disetujui')
                                                    bg-success
                                                @elseif ($izin->status_approval == 'Ditolak')
                                                    bg-danger
                                                @endif">
                                                {{ $izin->status_approval }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($izin->status_approval == 'Menunggu')
                                                <a href="#" class="btn btn-dark btn-sm approval" data-id="{{ $izin->id }}">
                                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('pengajuanizin.batal', $izin->id) }}" class="btn btn-danger btn-sm">
                                                    <i class="fa-solid fa-square-xmark"></i>
                                                </a>    
                                            @endif
                                            <form action="{{ route('pengajuanizin.delete', $izin->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete-izin"><i class="fa fa-trash"></i></button>
                                        </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted mt-2">Data tidak ditemukan</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $izins->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pengajuanizin" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pengajuan Izin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('pengajuanizin.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="id_izinform">
                        <div class="form-group">
                            <select name="status_approved" id="status_approved" class="form-control form-control-sm rounded">
                                <option value="" hidden>Pilih Status</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                            @error('status_approved')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-secondary btn-sm btn-block">
                            <i class="fas fa-save mx-2"></i>Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            // Logika untuk modal approval (tetap seperti sebelumnya)
            $(".approval").click(function(e) {
                e.preventDefault();
                var id_izin = $(this).data('id');
                $("#id_izinform").val(id_izin);
                $("#modal-pengajuanizin").modal("show");
            });

            $(".delete-izin").click(function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Yakin nih?',
                    text: 'Data pengajuan izin ini bakal lenyap selamanya lho!',
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