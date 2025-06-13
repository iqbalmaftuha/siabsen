<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: lightgray;
            font-family: 'Comic Sans MS', cursive, sans-serif;
        }
        .invalid-feedback {
            display: none;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
        .card {
            border-radius: 1rem;
        }
        .card-header {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container py-4 flex-grow-1">
        <div class="row justify-content-center">
            <!-- Foto Profil dan Info -->
            <div class="col-md-4 mt-4">
                <div class="card shadow border-primary">
                    <div class="card-body text-center p-4">
                        <h3 class="text-dark fw-bold mb-3">Profil</h3>
                        <img src="{{ $user->foto }}" alt="Foto Profil" class="rounded-circle mx-auto mb-4 border border-primary" width="100" height="100" loading="lazy">
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-2">{{ $user->nama_lengkap }}</h5>
                            <p class="text-muted fst-italic mb-0">{{ $user->jabatan }}</p>
                        </div>
                        <p class="card-text small mb-0">
                            Kemarin sudah berlalu.<br>
                            Esok belum datang.<br>
                            Hari ini belum diketahui.<br>
                            Ayo.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulir Update Profil -->
            <div class="col-md-8 my-4">
                <div class="card shadow border-primary">
                    <div class="card-header fw-bold p-3 border-primary">Update Profil</div>
                    <div class="card-body">
                        <form action="{{ route('profileadmin.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <!-- Nama Lengkap -->
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Jabatan -->
                            <div class="mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" required>
                                    @error('jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- No HP -->
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}">
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Password Baru -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="********">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Upload Foto Profil -->
                            <div class="mb-3">
                                <label for="profile-picture" class="form-label">Pilih Foto</label>
                                <input type="file" class="form-control @error('profile-picture') is-invalid @enderror" id="profile-picture" name="profile-picture" accept="image/*">
                                @error('profile-picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Tombol -->
                            <div class="row mt-4">
                                <div class="col-12 d-flex justify-content-between">
                                    <a href="{{ route('dashboardadmin') }}" class="btn btn-outline-primary w-auto">Kembali</a>
                                    <button type="submit" class="btn btn-primary w-auto">Update Profil</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-3">
        <div class="container position-relative text-center">
            <form action="{{ route('logout') }}" method="POST" class="position-absolute start-0 top-50 translate-middle-y ms-2 d-block d-md-none">
                @csrf
                <button type="submit" class="btn text-danger">
                    <i class="fa-solid fa-person-through-window"></i>
                </button>
            </form>
            <p class="mb-0">&copy; ADC 🌸🦋</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
</body>
</html>