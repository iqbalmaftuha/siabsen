@extends('layouts.app')

@section('content')
    <div class="page-header d-print-none mb-2">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Histori Absen</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Kolom kiri: hasil data -->
            <div class="col-12 col-md-9 order-2 order-md-1" id="showhistori">
                <div class="card bg-light shadow">
                    <div class="card-body" style="min-height: 60vh; max-height: 60vh; overflow-y: auto; padding: 0;">
                        <table class="table table-hover mb-0 text-center">
                            <thead class="table-secondary sticky-top">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Absen</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($histori->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Silakan pilih bulan dan tahun untuk melihat histori absen.
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($histori as $absen)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($absen->tgl_absen)->translatedFormat('d F Y') }}</td>
                                            <td>{{ $absen->jam_masuk }}</td>
                                            <td>{{ $absen->jam_pulang }}</td>
                                            <td>
                                                @if ($absen->status == 'Tepat Waktu')
                                                    <span class="badge bg-success w-50">Tepat Waktu</span>
                                                @elseif ($absen->status == 'Terlambat')
                                                    <span class="badge bg-danger text-dark w-50">Terlambat</span>
                                                @elseif ($absen->status == 'Izin')
                                                    <span class="badge bg-info text-dark w-50">Izin</span>
                                                @elseif ($absen->status == 'Sakit')
                                                    <span class="badge bg-warning text-dark w-50">Sakit</span>
                                                @else
                                                    <span class="badge bg-secondary w-50">Tidak Absen</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-muted">
                        <small>Terakhir diperbarui: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</small>
                    </div>
                </div>
            </div>

            <!-- Kolom kanan: form filter -->
            <div class="col-12 col-md-3 order-1 order-md-2">
                <form method="GET" action="{{ route('histori') }}">
                    <div class="form-group mb-2">
                        <label for="bulan" class="font-weight-bold">Pilih Bulan</label>
                        <select name="bulan" id="bulan" class="form-control bg-transparent border-secondary" required>
                            <option value="" hidden>Pilih Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="tahun" class="font-weight-bold">Pilih Tahun</label>
                        <input type="number" name="tahun" id="tahun" class="form-control bg-transparent border-secondary" value="{{ $tahun ?? now()->year }}" placeholder="Tahun" required>
                    </div>

                    <div class="form-group mb-4">
                        <button class="btn btn-secondary w-100" type="submit">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection