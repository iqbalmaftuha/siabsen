@extends('layouts.admin.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Heading -->
    <h1 class="h4 mb-4 text-gray-800 font-weight-bold">DASHBOARD</h1>

    <div class="container-fluid">
        <div class="row">
            {{-- Kartu Jumlah Pegawai --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary mb-1">JUMLAH TOTAL PEGAWAI</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPegawai }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Jumlah Hadir --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success mb-1">JUMLAH HADIR PERHARI</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahHadirPerHari }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Jumlah Izin --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success mb-1">JUMLAH IZIN PERHARI</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahIzinPerHari }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Jumlah Persentase --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary mb-1">PRESENTASE TERLAMBAT PERMINGGU</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $persentaseTerlambat }} %</div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar {{ $persentaseTerlambat > 50 ? 'bg-danger' : 'bg-info' }}" role="progressbar" style="width: {{ $persentaseTerlambat }}%" aria-valuenow="{{ $persentaseTerlambat }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-percent fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik & Leaderboard --}}
        <div class="row">
            {{-- Grafik Kehadiran --}}
            <div class="col-12 col-sm-8 mb-4 d-none d-md-block">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="font-weight-bold text-primary mb-1">GRAFIK KEHADIRAN PERMINGGU</div>
                        <div style="width: 100%; margin: auto;">
                            <canvas id="kehadiranChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Leaderboard --}}
            <div class="col-12 col-sm-4 mb-4 d-none d-md-block">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body" style="min-height: 60vh; max-height: 60vh; overflow-y: auto; padding: 0;">
                        <div class="font-weight-bold text-primary mb-1 px-3 pt-2 sticky-top bg-white">LEADERBOARD ABSEN</div>
                        <div class="list-group list-group-flush">
                            @forelse ($leaderboard as $item)
                            <div class="list-group-item border-0 px-3 py-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <img src="{{ $item->user->foto }}" alt="avatar" class="rounded-circle border border-primary" style="width: 40px; height: 40px; object-fit: cover;">
                                    </div>
                                    <div class="flex-grow-1 mx-3">
                                        <div class="fw-bold">{{ $item->user->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $item->user->jabatan }}</small>
                                    </div>
                                    <div>
                                        <span class="badge rounded-pill {{ $item->jam_masuk > $batasTepatWaktu ? 'bg-danger' : 'bg-info' }} text-white">
                                            {{ $item->jam_masuk }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <!-- Placeholder jika tidak ada data -->
                            <span class="text-center text-muted">
                                Tidak ada data absensi minggu ini.
                            </span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('myscript')
    <script>
        const hari = @json($hari);
        const tepatWaktu = @json($tepatWaktuPerHari);
        const terlambat = @json($terlambatPerHari);
        const totalPegawai = @json($totalPegawai);

        const ctx = document.getElementById('kehadiranChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hari,
                datasets: [
                    {
                        label: 'Tepat Waktu',
                        data: tepatWaktu,
                        backgroundColor: '#36b9cc',
                        borderColor: '#36b9cc',
                        borderWidth: 1
                    },
                    {
                        label: 'Terlambat',
                        data: terlambat,
                        backgroundColor: '#e74a3b',
                        borderColor: '#e74a3b',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: totalPegawai,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Pegawai'
                        },
                        stacked: true
                    },
                    x: {
                        stacked: true,
                        title: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endpush
