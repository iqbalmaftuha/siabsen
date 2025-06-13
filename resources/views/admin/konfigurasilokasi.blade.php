@extends('layouts.admin.app')

@section('title', 'Konfigurasi Lokasi')

@section('content')
    <!-- Page Heading -->
    <h1 class="h4 mb-4 text-gray-800 font-weight-bold">KONFIGURASI</h1>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <!-- Form Konfigurasi -->
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <div class="card shadow">
                        <div class="card-body">
                            <form action="{{ route('konfigurasi.update') }}" method="POST">
                                @csrf
                                <!-- Header Konfigurasi Jam Absen -->
                                <h6 class="font-weight-bold text-uppercase small mb-3">
                                    <i class="fas fa-clock mr-2"></i>Konfigurasi Jam Absen
                                </h6>

                                <div class="row">
                                    <!-- Form Jam Masuk -->
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="jamMasuk">
                                                <i class="fas fa-sign-in-alt mr-1"></i> Jam Masuk
                                            </label>
                                            <input type="time" step="1" class="form-control @error('jamMasuk') is-invalid @enderror" 
                                                   id="jamMasuk" name="jamMasuk">
                                            @error('jamMasuk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Form Jam Pulang -->
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="jamPulang">
                                                <i class="fas fa-sign-out-alt mr-1"></i> Jam Pulang
                                            </label>
                                            <input type="time" step="1" class="form-control @error('jamPulang') is-invalid @enderror" 
                                                   id="jamPulang" name="jamPulang">
                                            @error('jamPulang')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Header Konfigurasi Lokasi Kantor -->
                                <h6 class="font-weight-bold text-uppercase small mb-3 mt-4">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Konfigurasi Lokasi Kantor
                                </h6>

                                <!-- Form Lokasi Kantor -->
                                <div class="form-group">
                                    <label for="lokasiKantor">
                                        <i class="fas fa-building mr-1"></i> Lokasi Kantor
                                    </label>
                                    <input type="text" class="form-control @error('lokasiKantor') is-invalid @enderror" 
                                           id="lokasiKantor" name="lokasiKantor" 
                                           placeholder="Masukkan lokasi kantor (latitude,longitude)" 
                                           value="{{ old('lokasiKantor', $konfig->lokasi_kantor ?? '') }}">
                                    @error('lokasiKantor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Form Radius -->
                                <div class="form-group">
                                    <label for="radius">
                                        <i class="fas fa-circle mr-1"></i> Radius (meter)
                                    </label>
                                    <input type="number" class="form-control @error('radius') is-invalid @enderror" 
                                           id="radius" name="radius" 
                                           placeholder="Masukkan radius dalam meter" 
                                           value="{{ old('radius', $konfig->radius ?? '') }}">
                                    @error('radius')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tombol Update -->
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-block btn-primary">
                                        <i class="fas fa-save mr-2"></i>Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tampilan Konfigurasi Saat Ini -->
                <div class="col-12 col-md-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <!-- Header Jam Absen -->
                            <h6 class="font-weight-bold text-uppercase small mb-3">
                                <i class="fas fa-clock mr-2"></i>Jam Absen
                            </h6>

                            <div class="row mb-4">
                                <!-- Jam Masuk -->
                                <div class="col-12 col-sm-6 mb-3 mb-sm-0">
                                    <div class="card border-left-primary">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-sign-in-alt fa-2x text-primary mr-3"></i>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold">Jam Masuk</h6>
                                                    <p class="mb-0">{{ $konfig ? $konfig->jam_masuk_standar : '00:00:00' }} WIB</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Jam Pulang -->
                                <div class="col-12 col-sm-6">
                                    <div class="card border-left-danger">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-sign-out-alt fa-2x text-danger mr-3"></i>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold">Jam Pulang</h6>
                                                    <p class="mb-0">{{ $konfig ? $konfig->jam_pulang_standar : '00:00:00' }} WIB</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Header Peta -->
                            <h6 class="font-weight-bold text-uppercase small mb-3">
                                <i class="fas fa-map-marker-alt mr-2"></i>Lokasi Kantor
                            </h6>

                            <!-- Peta -->
                            <div id="map" style="height: 380px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([0, 0], 16); 

        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Event klik untuk mengisi input lokasiKantor
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            document.getElementById('lokasiKantor').value = lat + ',' + lng;
        });

        // Ambil data lokasi dan radius dari database
        @if ($konfig && $konfig->lokasi_kantor && $konfig->radius)
            // Parse lokasi_kantor (format: "latitude,longitude")
            var lokasi = "{{ $konfig->lokasi_kantor }}".split(',');
            var lat = parseFloat(lokasi[0]);
            var lng = parseFloat(lokasi[1]);
            var radius = parseFloat("{{ $konfig->radius }}");

            // Pastikan koordinat valid
            if (!isNaN(lat) && !isNaN(lng) && !isNaN(radius)) {
                // Set view peta ke lokasi kantor
                map.setView([lat, lng], 15);

                // Tambahkan marker untuk lokasi kantor
                var marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup('Lokasi Kantor')
                    .openPopup();

                // Tambahkan lingkaran untuk radius
                var circle = L.circle([lat, lng], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: radius // Radius dalam meter
                }).addTo(map);
            }
        @endif

        // Tampilkan pesan sukses
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Horeee!',
                html: '{{ session('success') }}',
                confirmButtonText: 'Mantap!'
            });
        @endif

        // Tampilkan pesan error
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Yahh, Gagal!',
                html: '{{ session('error') }}',
                confirmButtonText: 'Coba Lagi Deh'
            });
        @endif
    </script>
@endpush