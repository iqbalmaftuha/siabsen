@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="page-header d-print-none mb-2">
        <div class="container">
            <div class="row g-2 align-items-center">
                <div class="col d-flex justify-content-between">
                    <h2 class="page-title">Dashboard</h2>
                    <h2 id="clock"></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Presence Section -->
    <div class="container mt-2">
        <div class="row">
            <div class="col-6">
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #28a745, #20c997); border-radius: 28px;">
                    <div class="card-body text-white">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="me-3">
                                <i class="fas fa-camera-retro" style="font-size: 32px;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Masuk</h6>
                                <span class="small">{{ $jamMasuk }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #dc3545, #fd7e14); border-radius: 28px;">
                    <div class="card-body text-white">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="me-3">
                                <i class="fas fa-camera-retro" style="font-size: 32px;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Pulang</h6>
                                <span class="small">{{ $jamPulang }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recap Section -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3 fw-bold text-uppercase opacity-75 fs-6">
                    Rekap Presensi Bulan {{ $currentMonth }} Tahun {{ $currentYear }}
                </h5>
                <div class="row g-3">
                    <!-- Card Hadir -->
                    <div class="col-6 col-md-3">
                        <div class="card bg-light bg-opacity-25 shadow-sm text-center border-0">
                            <div class="card-body py-2 position-relative">
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle d-flex align-items-center justify-content-center" style="width: 1.4rem; height: 1.2rem;">{{ $hadir }}</span>
                                <i class="fas fa-user-check text-primary mb-2" style="font-size: 2rem;"></i>
                                <br><span class="small fw-medium">Hadir</span>
                            </div>
                        </div>
                    </div>
                    <!-- Card Izin -->
                    <div class="col-6 col-md-3">
                        <div class="card bg-light bg-opacity-25 shadow-sm text-center border-0">
                            <div class="card-body py-2 position-relative">
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle d-flex align-items-center justify-content-center" style="width: 1.4rem; height: 1.2rem;">{{ $izin }}</span>
                                <i class="fas fa-file-contract text-success mb-2" style="font-size: 2rem;"></i>
                                <br><span class="small fw-medium">Izin</span>
                            </div>
                        </div>
                    </div>
                    <!-- Card Sakit -->
                    <div class="col-6 col-md-3">
                        <div class="card bg-light bg-opacity-25 shadow-sm text-center border-0">
                            <div class="card-body py-2 position-relative">
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle d-flex align-items-center justify-content-center" style="width: 1.4rem; height: 1.2rem;">{{ $sakit }}</span>
                                <i class="fas fa-briefcase-medical text-warning mb-2" style="font-size: 2rem;"></i>
                                <br><span class="small fw-medium">Sakit</span>
                            </div>
                        </div>
                    </div>
                    <!-- Card Telat -->
                    <div class="col-6 col-md-3">
                        <div class="card bg-light bg-opacity-25 shadow-sm text-center border-0">
                            <div class="card-body py-2 position-relative">
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle d-flex align-items-center justify-content-center" style="width: 1.4rem; height: 1.2rem;">{{ $terlambat }}</span>
                                <i class="fas fa-clock text-danger mb-2" style="font-size: 2rem;"></i>
                                <br><span class="small fw-medium">Telat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-minggu-tab" data-bs-toggle="pill" data-bs-target="#pills-minggu" type="button" role="tab" aria-controls="pills-minggu" aria-selected="true">
                                    Minggu Ini
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-leaderboard-tab" data-bs-toggle="pill" data-bs-target="#pills-leaderboard" type="button" role="tab" aria-controls="pills-leaderboard" aria-selected="false">
                                    Leaderboard
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="pills-tabContent">
                            <!-- Tab Minggu Ini -->
                            <div class="tab-pane fade show active" id="pills-minggu" role="tabpanel" aria-labelledby="pills-minggu-tab">
                                <div class="list-group list-group-flush">
                                    @forelse ($absensiMingguIni as $absen)
                                    <div class="list-group-item border-0 px-3 py-2 d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-calendar-days text-white fs-5"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate me-2">{{ \Carbon\Carbon::parse($absen->tgl_absen)->format('d-m-Y') }}</div>
                                        <div class="d-flex gap-2">
                                            <span class="badge rounded-pill bg-success">{{ $absen->jam_masuk ?? '00:00:00' }}</span>
                                            <span class="badge rounded-pill bg-danger">{{ $absen->jam_pulang ?? '00:00:00' }}</span>
                                        </div>
                                    </div>
                                    @empty
                                    <!-- Placeholder jika tidak ada data -->
                                    <span class="list-group-item text-muted">
                                        Tidak ada data absensi minggu ini.
                                    </span>
                                    @endforelse
                                </div>
                            </div>
                            <!-- Tab Leaderboard -->
                            <div class="tab-pane fade" id="pills-leaderboard" role="tabpanel" aria-labelledby="pills-leaderboard-tab">
                                <div class="list-group list-group-flush">
                                    @forelse ($leaderboard as $item)
                                    <div class="list-group-item border-0 px-3 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="{{ $item->user->foto }}" alt="avatar" class="rounded-circle border border-secondary" style="width: 40px; height: 40px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">{{ $item->user->nama_lengkap }}</div>
                                                <small class="text-muted">{{ $item->user->jabatan }}</small>
                                            </div>
                                            <div>
                                                <span class="badge rounded-pill bg-success">{{ $item->jam_masuk }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <span class="list-group-item text-muted">
                                        Tidak ada data absensi hari ini.
                                    </span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clockElement = document.getElementById('clock');

            function updateClock() {
                const date = new Date();
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const seconds = String(date.getSeconds()).padStart(2, '0');
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;
            }

            updateClock();
            setInterval(updateClock, 1000);
        });
    </script>
@endpush