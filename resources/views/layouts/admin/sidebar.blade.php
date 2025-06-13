<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboardadmin') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">ADC Admin</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboardadmin') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Manajemen Data
    </div>

    <!-- Nav Item - Data Pegawai -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('data.pegawai') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Pegawai</span></a>
    </li>

    <!-- Nav Item - Izin Pegawai -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('pengajuan.izin') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Pengajuan Izin</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Manajemen Absensi
    </div>

    <!-- Nav Item - Monitoring Absensi -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('monitoring.absen') }}">
            <i class="fas fa-fw fa-eye"></i>
            <span>Monitoring Absensi</span></a>
    </li>

    <!-- Nav Item - Laporan Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="true" aria-controls="collapseLaporan">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Rekapitulasi Absensi</span>
        </a>
        <div id="collapseLaporan" class="collapse" aria-labelledby="headingLaporan" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Jenis Laporan :</h6>
                <a class="collapse-item" href="{{ route('rekapabsen.pegawai') }}">Laporan Pegawai</a>
                <a class="collapse-item" href="{{ route('rekap.absen') }}">Laporan Seluruh Pegawai</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        KONFIGURASI
    </div>

    <!-- Nav Item - Lokasi Kantor -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('konigurasi.lokasi') }}">
            <i class="fas fa-fw fa-map-marker-alt"></i>
            <span>Lokasi Kantor</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->