<!-- Top Navbar (Desktop Only) -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow py-1 d-none d-lg-flex sticky-top">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/img/cibungur.png') }}" alt="" width="120" class="me-2">
        </a>

        <!-- Navigation Items -->
        <div class="d-flex align-items-center ms-auto gap-4">
            <a href="{{ route('absen') }}" class="text-center text-decoration-none text-info">
                <i class="fa-solid fa-camera-retro"></i><br>
                <span class="small fw-medium">Absen</span>
            </a>
            <a href="{{ route('izin') }}" class="text-center text-decoration-none text-danger">
                <i class="fas fa-calendar-alt fs-6"></i><br>
                <span class="small fw-medium">Izin</span>
            </a>
            <a href="{{ route('histori') }}" class="text-center text-decoration-none text-warning">
                <i class="fas fa-file-alt fs-6"></i><br>
                <span class="small fw-medium">Histori</span>
            </a>

            <!-- Profile Dropdown -->
            <div class="dropdown text-center">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ Auth::user()->foto }}" alt="Foto Profil" class="rounded-circle me-2 border border-success rounded" style="width: 32px; height: 32px; object-fit: cover;">
                    <span class="fw-medium text-success small">Profil</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="{{ route('profil') }}">Profil Saya</a></li>
                    <li><a class="dropdown-item" href="{{ route('profil') }}">Edit Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Bottom Navbar (Mobile Only) -->
<div class="d-lg-none bg-white shadow fixed-bottom border-top rounded-pill my-3 mx-2">
    <div class="d-flex justify-content-around p-1">
        <a href="{{ route('dashboard') }}" class="text-center text-decoration-none text-dark">
            <i class="fa-solid fa-house"></i><br>
            <small>Home</small>
        </a>
        <a href="{{ route('absen') }}" class="text-center text-decoration-none text-info">
            <i class="fas fa-camera-retro fs-6"></i><br>
            <small>Absen</small>
        </a>
        <a href="{{ route('izin') }}" class="text-center text-decoration-none text-danger">
            <i class="fas fa-calendar-alt fs-6"></i><br>
            <small>Izin</small>
        </a>
        <a href="{{ route('histori') }}" class="text-center text-decoration-none text-warning">
            <i class="fas fa-file-alt fs-6"></i><br>
            <small>Histori</small>
        </a>
        <a href="{{ route('profil') }}" class="text-center text-decoration-none text-success">
            <i class="fas fa-user fs-6"></i><br>
            <small>Profil</small>
        </a>

        <!-- Logout Button -->
        {{-- <form action="{{ route('logout') }}" method="POST" class="text-center">
            @csrf
            <button type="submit" class="btn text-danger p-0">
                <i class="fas fa-sign-out-alt"></i><br>
                <small>Logout</small>
            </button>
        </form> --}}
    </div>
</div>