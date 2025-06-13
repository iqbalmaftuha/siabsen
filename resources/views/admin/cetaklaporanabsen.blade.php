<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Laporan Absensi</title>
  <!-- Normalize CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
  <!-- Paper.css untuk cetak -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Set ukuran kertas dan font -->
<style>
  @page { size: A4 landscape }
  body { font-family: 'Times New Roman', Times, serif; font-size: 12px; }
  .sheet { padding: 10mm; }
  .table-sm-custom { font-size: 10px; }
  .table-sm-custom th, .table-sm-custom td { padding: 4px; }
  .table img { width: 25px; height: 25px; object-fit: cover; }
  .kop-logo { width: 60px; height: auto; }
  .kop-text { flex-grow: 1; text-align: center; }
  .kop-text .fw-bold { font-size: 16px; } 
  .kop-text .fst-italic { font-size: 12px; } 
  .ttd { position: absolute; bottom: 10mm; right: 10mm; text-align: center; }
  .rekap-absensi { font-size: 10px; margin-top: 10px; }
  .rekap-absensi .fw-medium { width: 100px; }
  .table-sm-custom th[rowspan] { vertical-align: middle; text-align: center; }
  .table-sm-custom tbody td:nth-child(2),
  .table-sm-custom tbody td:nth-child(3) { text-align: left; }
</style>
</head>
<body class="A4 landscape">
  <section class="sheet padding-10mm">
    <div class="container bg-white">
      
      <!-- Kop Surat -->
      <div class="d-flex align-items-center border-bottom border-dark pb-2 mb-2 justify-content-center position-relative">
        <img src="{{ asset('assets/img/pandeglang.png') }}" alt="Logo Desa Cibungur" class="kop-logo position-absolute start-0">
        <div class="kop-text text-center">
          <div class="fw-bold text-uppercase">PEMERINTAH KABUPATEN PANDEGLANG</div>
          <div class="fw-bold">KECAMATAN SUKARESMI</div>
          <div class="fw-bold">DESA CIBUNGUR</div>
          <div class="fst-italic">Kp. Pamagersari, Jalan Panimbang RT. 03 RW. 01 Desa Cibungur</div>
        </div>
      </div>

      <!-- Judul Surat -->
      <div class="text-center my-3">
        <div class="fw-bold text-decoration-underline">
          LAPORAN ABSENSI BULAN {{ strtoupper(\Carbon\Carbon::create(null, $bulan)->translatedFormat('F')) }} {{ $tahun }}
        </div>
      </div>

      <!-- Tabel Rekap Absensi -->
      <div class="table-responsive">
        <table class="table table-bordered table-sm-custom text-center">
          <thead class="table-light">
            <tr>
              <th rowspan="2">No</th>
              <th rowspan="2">Nama Pegawai</th>
              <th rowspan="2">Jabatan</th>
              <th colspan="30">Tanggal</th>
              <th colspan="4">Total</th>
            </tr>
            <tr>
              @foreach ($tanggal as $hari)
                <th>{{ $hari }}</th>
              @endforeach
              <th>Hadir</th>
              <th>Izin</th>
              <th>Sakit</th>
              <th>Alpha</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pegawais as $pegawai)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pegawai->nama_lengkap }}</td>
                <td>{{ $pegawai->jabatan }}</td>

                <!-- Data absensi harian -->
                @foreach ($tanggal as $day)
                  <td>{{ $rekapAbsensi[$pegawai->nik]['harian'][$day] }}</td>
                @endforeach

                <!-- Total -->
                <td>{{ $rekapAbsensi[$pegawai->nik]['total']['hadir'] }}</td>
                <td>{{ $rekapAbsensi[$pegawai->nik]['total']['izin'] }}</td>
                <td>{{ $rekapAbsensi[$pegawai->nik]['total']['sakit'] }}</td>
                <td>{{ $rekapAbsensi[$pegawai->nik]['total']['alpha'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Keterangan -->
      <div class="rekap-absensi mt-2">
        <div class="fw-bold mb-1">KETERANGAN</div>
        <div class="d-flex"><div class="fw-medium" style="width: 16px;">H</div><div>: Hadir</div></div>
        <div class="d-flex"><div class="fw-medium" style="width: 16px;">I</div><div>: Izin</div></div>
        <div class="d-flex"><div class="fw-medium" style="width: 16px;">S</div><div>: Sakit</div></div>
        <div class="d-flex"><div class="fw-medium" style="width: 16px;">A</div><div>: Alfa</div></div>
      </div>

      <!-- Tanda Tangan -->
      <div class="ttd mt-5">
        <div>Cibungur, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <div>Kepala Desa Cibungur,</div>
        <div class="mt-5 fw-bold text-decoration-underline">H. MUHI, SE</div>
      </div>

    </div>
  </section>
</body>

</html>