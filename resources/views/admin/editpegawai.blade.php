<form action="{{ route('datapegawai.update', $pegawai->nik) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12 px-4">
            <!-- NIK -->
            <div class="input-group input-group-sm mt-2">
                <span class="input-group-text"><i class="fas fa-id-card text-primary"></i></span>
                <input type="text" class="form-control rounded @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ $pegawai->nik }}" placeholder="NIK" readonly>
            </div>
            <!-- Nama Lengkap -->
            <div class="input-group input-group-sm mt-4">
                <span class="input-group-text"><i class="fas fa-user text-primary"></i></span>
                <input type="text" class="form-control rounded @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ $pegawai->nama_lengkap }}" placeholder="Nama Lengkap">
            </div>
            <!-- Jabatan -->
            <div class="input-group input-group-sm mt-4">
                <span class="input-group-text"><i class="fas fa-briefcase text-primary"></i></span>
                <input type="text" class="form-control rounded @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ $pegawai->jabatan }}" placeholder="Jabatan">
            </div>
            <!-- No HP -->
            <div class="input-group input-group-sm mt-4">
                <span class="input-group-text"><i class="fas fa-phone text-primary"></i></span>
                <input type="tel" class="form-control rounded @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ $pegawai->no_hp }}" placeholder="Nomor HP">
            </div>
            <!-- Email -->
            <div class="input-group input-group-sm mt-4">
                <span class="input-group-text"><i class="fas fa-envelope text-primary"></i></span>
                <input type="email" class="form-control rounded @error('email') is-invalid @enderror" id="email" name="email" value="{{ $pegawai->email }}" placeholder="Email">
            </div>
            <!-- Password -->
            <div class="input-group input-group-sm mt-4">
                <span class="input-group-text"><i class="fas fa-lock text-primary"></i></span>
                <input type="password" class="form-control rounded @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password (kosongkan jika tidak ingin mengubah)">
            </div>
            <!-- Role -->
            <div class="input-group input-group-sm mt-4">
                <span class="input-group-text"><i class="fas fa-user-tag text-primary"></i></span>
                <select class="form-control rounded @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="" hidden>Pilih Role</option>
                    <option value="pegawai" {{ $pegawai->role == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    <option value="admin" {{ $pegawai->role == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <!-- Foto -->
            <div class="input-group input-group-sm mt-4">
                <span class="input-group-text"><i class="fas fa-image text-primary"></i></span>
                <input type="file" class="form-control rounded @error('foto') is-invalid @enderror" id="foto" name="foto">
            </div>
            <!-- Preview Foto -->
            <div class="my-4">
                <img src="{{ $pegawai->foto }}" alt="Foto Profil" style="width: 100px; height: 100px; object-fit: cover;">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Kembali</button>
        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
    </div>
</form>