<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Laporan Absensi Pegawai</title>
  <!-- Normalize CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
  <!-- Paper.css untuk cetak -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Set ukuran kertas dan font -->
  <style>
    @page { size: A4 }
    body { font-family: 'Times New Roman', Times, serif; font-size: 12px; }
    .sheet { padding: 10mm; }
    .table-sm-custom { font-size: 10px; }
    .table-sm-custom th, .table-sm-custom td { padding: 4px; }
    .table img { width: 25px; height: 25px; object-fit: cover; }
    .table-sm-custom td:last-child { font-size: 8px; white-space: normal; word-wrap: break-word; }
    .kop-logo { width: 60px; height: auto; }
    .kop-text { flex-grow: 1; text-align: center; }
    .kop-text .fw-bold { font-size: 16px; } 
    .kop-text .fst-italic { font-size: 12px; } 
    .ttd { position: absolute; bottom: 10mm; right: 10mm; text-align: center; }
    .rekap-absensi { font-size: 10px; margin-top: 10px; }
    .rekap-absensi .fw-medium { width: 100px; }
  </style>
</head>
<body class="A4">
  <section class="sheet position-relative">
    <div class="container bg-white">
      <!-- Kop Surat -->
      <div class="d-flex align-items-center border-bottom border-dark pb-2 mb-2 justify-content-center position-relative">
        <img src="{{ asset('assets/img/pandeglang.png') }}" alt="Logo Desa Cibungur" class="kop-logo position-absolute start-0">
        <div class="kop-text  text-center">
          <div class="fw-bold text-uppercase">PEMERINTAH KABUPATEN PANDEGLANG</div>
          <div class="fw-bold">KECAMATAN SUKARESMI</div>
          <div class="fw-bold">DESA CIBUNGUR</div>
          <div class="fst-italic">Kp. Pamagersari, Jalan Panimbang RT. 03 RW. 01 Desa Cibungur</div>
        </div>
      </div>

      <!-- Judul Surat -->
      <div class="text-center my-3">
        <div class="fw-bold text-decoration-underline">LAPORAN ABSENSI PEGAWAI {{ strtoupper(\Carbon\Carbon::create(null, $bulan)->translatedFormat('F')) }} {{ $tahun }}</div>
      </div>

      <!-- Data Pegawai -->
      <div class="d-flex mb-4">
        <img src="{{ $pegawai->foto }}" alt="Foto Karyawan" class="me-3" style="width: 60px; height: 70px; object-fit: cover;">
        <div>
          <div class="d-flex">
            <div class="fw-medium" style="width: 100px;">NIK</div>
            <div>: {{ $pegawai->nik }}</div>
          </div>
          <div class="d-flex">
            <div class="fw-medium" style="width: 100px;">Nama Lengkap</div>
            <div>: {{ $pegawai->nama_lengkap }}</div>
          </div>
          <div class="d-flex">
            <div class="fw-medium" style="width: 100px;">Jabatan</div>
            <div>: {{ $pegawai->jabatan }}</div>
          </div>
          <div class="d-flex">
            <div class="fw-medium" style="width: 100px;">Email</div>
            <div>: {{ $pegawai->email }}</div>
          </div>
        </div>
      </div>

      <!-- Tabel Absensi -->
      <div class="table-responsive">
        <table class="table table-bordered table-sm-custom text-center">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th style="width: 10%;">Tanggal</th>
              <th style="width: 10%;">Hari</th>
              <th style="width: 10%;">Status</th>
              <th style="width: 10%;">Jam Masuk</th>
              <th style="width: 15%;">Foto Masuk</th>
              <th style="width: 10%;">Jam Pulang</th>
              <th style="width: 15%;">Foto Pulang</th>
              <th style="width: 20%;">Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rekapdata as $data)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $data->tanggal }}</td>
              <td>{{ $data->hari }}</td>
              <td>{{ $data->status }}</td>
              <td>{{ $data->jam_masuk }}</td>
              <td>
                @if ($data->foto_masuk != '-')
                <img src="{{ $data->foto_masuk }}" alt="Foto Masuk">
                @else
                {{ $data->foto_masuk }}
                @endif
              </td>
              <td>{{ $data->jam_pulang }}</td>
              <td>
                @if ($data->foto_pulang != '-')
                <img src="{{ $data->foto_pulang }}" alt="Foto Masuk">
                @else
                {{ $data->foto_pulang }}
                @endif
              </td>
              <td>{{ $data->keterangan }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Rekap Absensi -->
      <div class="rekap-absensi">
        <div class="fw-bold mb-1">REKAP ABSENSI</div>
        <div class="d-flex">
          <div class="fw-medium" style="width: 100px;">Hadir</div>
          <div>: {{ $rekap['hadir'] }} hari</div>
        </div>
        <div class="d-flex">
          <div class="fw-medium" style="width: 100px;">Izin</div>
          <div>: {{ $rekap['izin'] }} hari</div>
        </div>
        <div class="d-flex">
          <div class="fw-medium" style="width: 100px;">Sakit</div>
          <div>: {{ $rekap['sakit'] }} hari</div>
        </div>
        <div class="d-flex">
          <div class="fw-medium" style="width: 100px;">Alfa</div>
          <div>: {{ $rekap['alfa'] }} hari</div>
        </div>
      </div>

      <!-- Tanda Tangan -->
      <div class="ttd">
        <div>Cibungur, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <div>Kepala Desa Cibungur,</div>
        <div class="mt-5 fw-bold text-decoration-underline">H. MUHI, SE</div>
      </div>
    </div>
  </section>
</body>
</html>