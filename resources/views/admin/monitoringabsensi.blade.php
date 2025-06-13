@extends('layouts.admin.app')

@section('title', 'Monitoring Absensi')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #map { height: 180px; }
</style>
@section('content')
    <!-- Page Heading -->
    <h1 class="h4 mb-4 text-gray-800 font-weight-bold">MONITORING ABSENSI</h1>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="text-xs font-weight-bold text-white mb-0">FILTER DATA ABSEN</h4>
                    </div>
                    <div class="card-body">
                        <!-- Form Pencarian -->
                        <form action="{{ route('monitoring.absen') }}" method="GET" autocomplete="off">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-days"></i></span>
                                        </div>
                                        <input type="number" class="form-control" id="tanggal" name="tanggal" placeholder="Tanggal" min="0" max="31" pattern="\d{1,2}" value="{{ request('tanggal') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-days"></i></span>
                                        </div>
                                        <input type="number" class="form-control" id="bulan" name="bulan" placeholder="Bulan" min="0" max="12" step="1" pattern="\d{1,2}" value="{{ request('bulan') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-days"></i></span>
                                        </div>
                                        <input type="number" class="form-control" id="tahun" name="tahun" placeholder="Tahun" min="1999" max="2999" step="1" pattern="\d{4}" value="{{ request('tahun') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-4 mb-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="nik" name="nik" placeholder="Nik" value="{{ request('nik') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" value="{{ request('nama_lengkap') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-2">
                                    <button class="btn btn-primary btn-sm btn-block" type="submit">
                                        <i class="fas fa-magnifying-glass mx-2"></i>Cari Data
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!-- Tabel Data -->
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan</th>
                                        <th>Tanggal Absen</th>
                                        <th>Jam Masuk</th>
                                        <th>Foto Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Foto Pulang</th>
                                        <th>Keterangan</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($absensis as $index => $absensi)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $absensi->nik }}</td>
                                            <td>{{ $absensi->user->nama_lengkap ?? '-' }}</td>
                                            <td>{{ $absensi->user->jabatan ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($absensi->tgl_absen)->format('d-m-Y') }}</td>
                                            <td>{{ $absensi->jam_masuk ?? '00:00:00' }}</td>
                                            <td>
                                                <img src="{{ asset('storage/uploads/absensi/' . $absensi->foto_masuk) }}" alt="Foto Masuk" width="60" height="40">
                                            </td>
                                            <td>{{ $absensi->jam_pulang ?? '00:00:00' }}</td>
                                            <td>
                                                @if ($absensi->foto_pulang && file_exists(public_path('storage/uploads/absensi/' . $absensi->foto_pulang)))
                                                    <img src="{{ asset('storage/uploads/absensi/' . $absensi->foto_pulang) }}" alt="Foto Pulang" width="60" height="40">
                                                @else
                                                    <i class="fas fa-image fa-2x"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge w-100 text-light {{ $absensi->status_masuk == 'Tepat Waktu' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $absensi->status_masuk }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-primary btn-sm btn-updateabsen mr-1" 
                                                        data-id="{{ $absensi->id }}"
                                                        data-nik="{{ $absensi->nik }}"
                                                        data-nama="{{ $absensi->user->nama_lengkap ?? '-' }}"
                                                        data-jabatan="{{ $absensi->user->jabatan ?? '-' }}"
                                                        data-tanggal="{{ $absensi->tgl_absen }}"
                                                        data-jammasuk="{{ $absensi->jam_masuk }}"
                                                        data-jampulang="{{ $absensi->jam_pulang }}"
                                                        data-status="{{ $absensi->status_masuk }}"
                                                        data-lokasi="{{ $absensi->lokasi_masuk }}"
                                                        data-fotomasuk="{{ asset('storage/uploads/absensi/' . $absensi->foto_masuk) }}"
                                                        data-fotopulang="{{ $absensi->foto_pulang ? asset('storage/uploads/absensi/' . $absensi->foto_pulang) : '' }}"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('monitoringabsen.delete', $absensi->id) }}" method="POST" class="delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted mt-2">Data tidak ditemukan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Paginasi -->
                        <div class="d-flex justify-content-center">
                        {{ $absensis->appends(request()->query())->links() }}
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-updateabsen" tabindex="-1" role="dialog" aria-labelledby="modalUpdateAbsenTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('monitoringabsen.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalUpdateAbsenTitle">Update Data Absensi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id_updateabsen" name="id">

                        <div class="row">
                            <div class="col-12 px-4">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text"><i class="fas fa-id-card text-primary"></i></span>
                                    <input type="text" id="nik_updateabsen" class="form-control rounded" name="nik" readonly>
                                </div>
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text"><i class="fas fa-user text-primary"></i></span>
                                    <input type="text" id="nama_updateabsen" class="form-control rounded" name="nama_lengkap" readonly>
                                </div>
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text"><i class="fas fa-briefcase text-primary"></i></span>
                                    <input type="text" id="jabatan_updateabsen" class="form-control rounded" name="jabatan" readonly>
                                </div>
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt text-primary"></i></span>
                                    <input type="date" id="tanggal_updateabsen" class="form-control rounded" name="tanggal_absen" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fas fa-clock text-primary"></i></span>
                                            <input type="time" step="1" id="jammasuk_updateabsen" class="form-control rounded" name="jam_masuk">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fas fa-clock text-primary"></i></span>
                                            <input type="time" step="1" id="jampulang_updateabsen" class="form-control rounded" name="jam_pulang">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6 text-center">
                                        <img src="#" class="img-fluid rounded" id="foto_masuk" alt="Foto Masuk" style="width: 120px; height: auto; object-fit: cover;">
                                        <i class="fas fa-image fa-4x text-muted" id="icon_foto_masuk"></i>
                                    </div>
                                    <div class="col-6 text-center">
                                        <img src="#" class="img-fluid rounded" id="foto_pulang" alt="Foto Pulang" style="width: 120px; height: auto; object-fit: cover;">
                                        <i class="fas fa-image fa-4x text-muted" id="icon_foto_pulang"></i>
                                    </div>
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text"><i class="fas fa-info-circle text-primary"></i></span>
                                    <select id="status_updateabsen" name="status_masuk" class="form-control rounded">
                                        <option value="Tepat Waktu">Tepat Waktu</option>
                                        <option value="Terlambat">Terlambat</option>
                                    </select>
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text"><i class="fas fa-building text-primary"></i></span>
                                    <input type="text" id="lokasi_updateabsen" class="form-control rounded" name="lokasi_masuk">
                                </div>

                                <div id="map"></div>
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
@endsection

@push('myscript')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>

        // Script SweetAlert untuk menampilkan pesan sukses atau error
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Mantap!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Ups!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        @endif

    // SweetAlert untuk konfirmasi hapus
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'Yakin nih?',
                    text: 'Data ini bakal lenyap selamanya lho!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus aja!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

        $(function() {
            let map = null; // Variabel untuk menyimpan instance peta

            $(".btn-updateabsen").click(function(e) {
                e.preventDefault();

                // Ambil data dari atribut data-*
                let id = $(this).data('id');
                let nik = $(this).data('nik');
                let nama = $(this).data('nama');
                let jabatan = $(this).data('jabatan');
                let tanggal = $(this).data('tanggal');
                let jamMasuk = $(this).data('jammasuk');
                let jamPulang = $(this).data('jampulang');
                let status = $(this).data('status');
                let lokasi = $(this).data('lokasi');
                let fotoMasuk = $(this).data('fotomasuk');
                let fotoPulang = $(this).data('fotopulang');

                // Isi data ke dalam form modal
                $("#id_updateabsen").val(id);
                $("#nik_updateabsen").val(nik);
                $("#nama_updateabsen").val(nama);
                $("#jabatan_updateabsen").val(jabatan);
                $("#tanggal_updateabsen").val(tanggal);
                $("#jammasuk_updateabsen").val(jamMasuk);
                $("#jampulang_updateabsen").val(jamPulang);
                $("#status_updateabsen").val(status);
                $("#lokasi_updateabsen").val(lokasi);

                // Handle foto masuk
                if (fotoMasuk) {
                    $("#foto_masuk").attr('src', fotoMasuk).show();
                    $("#icon_foto_masuk").hide();
                } else {
                    $("#foto_masuk").hide();
                    $("#icon_foto_masuk").show();
                }

                // Handle foto pulang
                if (fotoPulang) {
                    $("#foto_pulang").attr('src', fotoPulang).show();
                    $("#icon_foto_pulang").hide();
                } else {
                    $("#foto_pulang").hide();
                    $("#icon_foto_pulang").show();
                }

                // Tampilkan modal
                $("#modal-updateabsen").modal("show");

                // Inisialisasi peta setelah modal ditampilkan
                $('#modal-updateabsen').on('shown.bs.modal', function () {
                    // Hapus peta lama jika ada
                    if (map !== null) {
                        map.remove();
                    }

                    // Inisialisasi peta baru
                    map = L.map('map');

                    // Periksa apakah lokasi valid
                    if (lokasi) {
                        let lokUser = lokasi.split(",");
                        let latitudeUser = parseFloat(lokUser[0]);
                        let longitudeUser = parseFloat(lokUser[1]);

                        // Ambil data lokasi kantor dan radius dari konfiglok
                        let lokasiKantor = "{{ $konfig->lokasi_kantor ?? '' }}";
                        let radiusKantor = {{ $konfig->radius ?? 0 }};
                        let lokKantor = lokasiKantor.split(",");
                        let latitudeKantor = parseFloat(lokKantor[0]);
                        let longitudeKantor = parseFloat(lokKantor[1]);

                        // Pastikan koordinat pengguna valid
                        if (!isNaN(latitudeUser) && !isNaN(longitudeUser)) {
                            // Atur tampilan peta ke lokasi pengguna
                            map.setView([latitudeUser, longitudeUser], 18);

                            // Tambahkan layer peta dari OpenStreetMap
                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            // Tambahkan penanda (marker) pada lokasi pengguna
                            L.marker([latitudeUser, longitudeUser]).addTo(map)
                                .bindPopup('Lokasi Absen').openPopup();

                            // Tambahkan penanda dan lingkaran untuk lokasi kantor
                            if (!isNaN(latitudeKantor) && !isNaN(longitudeKantor)) {
                                L.marker([latitudeKantor, longitudeKantor]).addTo(map)
                                    .bindPopup('Lokasi Kantor').openPopup();

                                // Tambahkan lingkaran hanya untuk radius kantor (warna merah)
                                L.circle([latitudeKantor, longitudeKantor], {
                                    color: 'red',
                                    fillColor: '#f03',
                                    fillOpacity: 0.3,
                                    radius: radiusKantor
                                }).addTo(map);
                            }

                            // Tambahkan event click untuk memperbarui input lokasi
                            map.on('click', function(e) {
                                var lat = e.latlng.lat;
                                var lng = e.latlng.lng;
                                // Perbarui input lokasi di form
                                $("#lokasi_updateabsen").val(lat + ',' + lng);
                                // Perbarui posisi marker pengguna
                                userMarker.setLatLng([lat, lng]);
                                // Perbarui popup
                                userMarker.bindPopup('Lokasi Absen: ' + lat + ', ' + lng).openPopup();
                            });

                            // Perbaiki tampilan peta agar sesuai ukuran container
                            setTimeout(() => {
                                map.invalidateSize();
                            }, 200);
                        }
                    }
                });

                // Bersihkan event handler setelah modal ditutup
                $('#modal-updateabsen').on('hidden.bs.modal', function () {
                    if (map !== null) {
                        map.remove();
                        map = null;
                    }
                });
            });
        });
    </script>
@endpush