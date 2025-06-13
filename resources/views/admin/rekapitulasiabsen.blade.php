@extends('layouts.admin.app')

@section('title', 'Konfigurasi Lokasi')

@section('content')
    <!-- Page Heading -->
    <h1 class="h4 mb-4 text-gray-800 font-weight-bold">REKAPITULASI ABSENSI</h1>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h6 class="font-weight-bold text-white my-0">LAPORAN ABSENSI SELURUH PEGAWAI</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('rekapabsen.laporan') }}" target="_blank"  method="POST">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="bulan" class="font-weight-bold"><i class="fas fa-calendar-alt mr-2"></i>Bulan</label>
                                <select class="form-control custom-select" id="bulan" name="bulan" required>
                                    <option value="" hidden>Pilih Bulan</option>
                                    @foreach ($bulan as $key => $namabulan)
                                        <option value="{{ $key }}" {{ old('bulan') == $key ? 'selected' : '' }}>
                                            {{ $namabulan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <label for="tahun" class="font-weight-bold"><i class="fas fa-calendar-check mr-2"></i>Tahun</label>
                                <select class="form-control custom-select" id="tahun" name="tahun" required>
                                    <option value="" hidden>Pilih Tahun</option>
                                    @foreach ($tahunrange as $tahun)
                                        <option value="{{ $tahun }}" {{ old('tahun') == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <button class="btn btn-primary btn-block"><i class="fas fa-print mr-2"></i>Cetak</button>
                                </div>
                                {{-- <div class="col-6 mb-2">
                                    <button class="btn btn-success btn-block"><i class="fas fa-file-excel mr-2"></i>Unduh Excel</button>
                                </div> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection