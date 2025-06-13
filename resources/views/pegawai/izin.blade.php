@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="page-header d-print-none mb-2">
        <div class="container">
            <div class="row g-2 align-items-center">
                <div class="col d-flex justify-content-between">
                    <h2 class="page-title">Izin</h2>
                    <div class="position-relative">
                        <!-- Tombol untuk membuka modal -->
                        <button class="btn btn-outline-secondary shadow d-flex align-items-center px-4" data-bs-toggle="modal" data-bs-target="#izinModal">
                            Mau Izin
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Form Izin -->
    <div class="modal fade" id="izinModal" tabindex="-1" aria-labelledby="izinModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="izinModalLabel">Form Izin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form untuk mengisi izin -->
                    <form action="{{ route('pegawai.storeIzin') }}" method="POST" id="izinForm">
                        @csrf
                        <div class="mb-3">
                            <label for="tanggal_izin" class="form-label">Tanggal Izin</label>
                            <input type="date" class="form-control @error('tanggal_izin') is-invalid @enderror" id="tanggal_izin" name="tanggal_izin" value="{{ old('tanggal_izin') }}">
                            @error('tanggal_izin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="" hidden>Pilih Status</option>
                                <option value="Izin" {{ old('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                                <option value="Sakit" {{ old('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="alasan" class="form-label">Alasan Izin</label>
                            <textarea class="form-control @error('alasan') is-invalid @enderror" id="alasan" name="alasan" rows="3">{{ old('alasan') }}</textarea>
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-secondary">Kirim Izin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Menampilkan Data Izin -->
    <div class="container mt-3">
        @if ($izin->isEmpty())
            <div class="alert alert-secondary shadow">Belum ada data izin, bro!</div>
        @else
            <ul class="list-group">
                @foreach ($izin as $item)
                    <li class="list-group-item px-4 shadow">
                        <div class="d-flex align-items-center">
                            <span class="me-4 text-primary">{{ $loop->iteration + $izin->firstItem() -1 }}</span>
                            <!-- Icon Section -->
                            <div class="me-3">
                                <i class="fas fa-user-md fs-2"></i>
                            </div>
                            <!-- Main Content Section -->
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }} | {{ $item->status }}</div>
                                <small class="text-muted">{{ $item->keterangan }}</small>
                            </div>
                            <!-- Badge Section -->
                            <div>
                                @if ($item->status_approval == 'Menunggu')
                                    <span class="badge rounded-pill bg-warning" style="min-width: 80px">Menunggu</span>
                                @elseif ($item->status_approval == 'Disetujui')
                                    <span class="badge rounded-pill bg-success" style="min-width: 80px">Disetujui</span>
                                @elseif ($item->status_approval == 'Ditolak')
                                    <span class="badge rounded-pill bg-danger" style="min-width: 80px">Ditolak</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
        <div class="mt-4">
            {{ $izin->links() }}
        </div>
    </div>
@endsection

@push('myscript')
<!-- SweetAlert2 Script untuk Pesan -->
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonText: 'Oke, Bro!'
        });
    </script>
@endif
@if (session('error') || $errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Aduh!',
            text: '{{ session('error') ?? ($errors->first() ?? 'Ada yang salah, bro! Cek lagi datanya.') }}',
            confirmButtonText: 'Coba Lagi, Bro!'
        }).then(() => {
            // Buka modal otomatis kalau ada error
            var izinModal = new bootstrap.Modal(document.getElementById('izinModal'));
            izinModal.show();
        });
    </script>
@endif

<!-- Konfirmasi Submit Form -->
<script>
    document.getElementById('izinForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin, Bro?',
            text: 'Pastikan data sudah benar sebelum kirim!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endpush