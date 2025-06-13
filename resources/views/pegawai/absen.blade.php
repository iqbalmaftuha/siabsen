@extends('layouts.app')

<style>
    .webcam-capture,
    .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        margin: auto;
        height: auto !important;
        border-radius: 10px;
    }

    #map { height: 400px; }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

@section('content')
    <div class="page-header d-print-none mb-2">
        <div class="container">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Absensi</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container">
            <div class="row">
                <!-- Card sebelah kiri -->
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <!-- Webcam Capture -->
                            <div class="text-center">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <input type="hidden" id="lokasi">
                                    <div class="webcam-capture"></div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <button id="absenMasuk" class="btn btn-primary w-100" {{ $cek > 0 ? 'disabled' : '' }}>
                                        <i class="fa-solid fa-hand-point-right"></i>
                                        Masuk
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button id="absenPulang" class="btn btn-danger w-100" {{ $cek == 0 ? 'disabled' : '' }}>
                                        Pulang
                                        <i class="fa-solid fa-hand-point-left"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card sebelah kanan -->
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header">Peta Lokasi</div>
                        <div class="card-body">
                            <div id="map" style="height: 420px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>

        // Cek apakah ada pesan error dari controller
        @if (session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error'
            });
        @endif

        // Konfigurasi Webcam
        Webcam.set({
            width: 640,
            height: 360,
            image_format: 'jpeg',
            jpeg_quality: 80
        });

        // Menyambungkan kamera ke elemen dengan class 'webcam-capture'
        Webcam.attach('.webcam-capture');

        // Untuk Map Lokasi
        var lokasi = document.getElementById('lokasi');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }

        function successCallback(position) {
            lokasi.value = position.coords.latitude + "," + position.coords.longitude;
            var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 13);
            var lokasi_kantor = "{{ $lok_kantor->lokasi_kantor ?? '' }}";
            var lok = lokasi_kantor.split(",");
            var lat_kantor = lok[0];
            var long_kantor = lok[1];
            var radius = "{{ $lok_kantor->radius ?? 0 }}";

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Tanda untuk lokasi pengguna
            var userMarker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map)
                .bindPopup('Lokasi Anda').openPopup();

            // Tanda untuk lokasi kantor
            var kantorMarker = L.marker([lat_kantor, long_kantor]).addTo(map)
                .bindPopup('Lokasi Kantor').openPopup();

            // Lingkaran radius kantor
            var circle = L.circle([lat_kantor, long_kantor], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: radius
            }).addTo(map);
        }

        function errorCallback() {
            Swal.fire({
                title: 'Error!',
                text: 'Gagal mendapatkan lokasi. Pastikan GPS diaktifkan.',
                icon: 'error'
            });
        }

        // Event untuk tombol Absen Masuk
        $("#absenMasuk").click(function(e) {
            e.preventDefault();
            kirimAbsensi('masuk');
        });

        // Event untuk tombol Absen Pulang
        $("#absenPulang").click(function(e) {
            e.preventDefault();
            kirimAbsensi('pulang');
        });

        // Fungsi untuk mengirim data absensi
        function kirimAbsensi(tipe) {
            Webcam.snap(function(uri) {
                var image = uri;
                var lokasi = $("#lokasi").val();

                // Pengiriman data menggunakan AJAX
                $.ajax({
                    type: 'POST',
                    url: '{{ route('absensi.store') }}',
                    data: {
                        _token: "{{ csrf_token() }}",
                        image: image,
                        lokasi: lokasi,
                        tipe: tipe // Kirim tipe absensi (masuk/pulang)
                    },
                    cache: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Yeay, Mantap Jiwa!',
                                text: response.message,
                                icon: 'success'
                            });
                            setTimeout(() => {
                                location.href = '{{ route('dashboard') }}';
                            }, 3000);
                        } else {
                            Swal.fire({
                                title: 'Aduh, Gagal Deh!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            });
        }
    </script>
@endpush