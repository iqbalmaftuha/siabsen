<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Konfiglok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboardAdmin()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->addDays(4);
        $totalPegawai = User::where('role', 'pegawai')->count();
        $batasTepatWaktu = Konfiglok::value('jam_masuk_standar') ?? '00:00:00';
        $jumlahHadirPerHari = Absensi::whereDate('tgl_absen', $today)
            ->whereNotNull('jam_masuk')
            ->whereHas('user', function ($query) {
                $query->where('role', 'pegawai');
            })
            ->count();
        $jumlahIzinPerHari = Izin::whereDate('tanggal', $today)
            ->whereHas('user', function ($query) {
                $query->where('role', 'pegawai');
            })
            ->count();
        $leaderboard = Absensi::with('user')
            ->where('tgl_absen', $today)
            ->whereHas('user', function ($query) {
                $query->where('role', 'pegawai');
            })
            ->orderBy('jam_masuk', 'asc')
            ->get();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $tepatWaktuPerHari = [];
        $terlambatPerHari = [];
        $totalTerlambatMinggu = 0;

        for ($i = 0; $i < 5; $i++) {
            $tanggal = $startOfWeek->copy()->addDays($i);
            $tepatWaktu = Absensi::whereDate('tgl_absen', $tanggal)
                ->whereNotNull('jam_masuk')
                ->whereTime('jam_masuk', '<=', $batasTepatWaktu)
                ->whereHas('user', function ($query) {
                    $query->where('role', 'pegawai');
                })
                ->count();
            $terlambat = Absensi::whereDate('tgl_absen', $tanggal)
                ->whereNotNull('jam_masuk')
                ->whereTime('jam_masuk', '>', $batasTepatWaktu)
                ->whereHas('user', function ($query) {
                    $query->where('role', 'pegawai');
                })
                ->count();
            $tepatWaktuPerHari[] = $tepatWaktu;
            $terlambatPerHari[] = $terlambat;
            $totalTerlambatMinggu += $terlambat;
        }

        $persentaseTerlambat = $totalPegawai > 0 ? round(($totalTerlambatMinggu / ($totalPegawai * 5)) * 100, 2) : 0;

        return view('admin.dashboardadmin', compact('hari', 'tepatWaktuPerHari', 'terlambatPerHari', 'totalPegawai', 'jumlahHadirPerHari', 'jumlahIzinPerHari', 'persentaseTerlambat', 'leaderboard', 'batasTepatWaktu'));
    }

    public function dataPegawai(Request $request)
    {
        $search = $request->query('nama_pegawai');
        $pegawai = User::when($search, function ($query, $search) {
            return $query->where('nama_lengkap', 'like', '%' . $search . '%');
        })->orderBy('nama_lengkap')->paginate(10);

        return view('admin.datapegawai', compact('pegawai'));
    }

    public function storePegawai(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:users,nik',
            'nama_lengkap' => 'required|string|max:20',
            'jabatan' => 'required|string|max:20',
            'no_hp' => 'nullable|string|max:15|regex:/^[\+]?[0-9]{10,15}$/',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,pegawai',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nik.required' => 'NIK wajib diisi, bro, jangan lupa!',
            'nik.size' => 'NIK harus pas 16 karakter, jangan kurang, jangan lebih!',
            'nik.unique' => 'Waduh, NIK ini udah dipake, cari yang lain dong!',
            'nama_lengkap.required' => 'Nama lengkap kosong? Serius, bro?',
            'nama_lengkap.max' => 'Nama lengkap maksimal 20 karakter, singkat aja ya!',
            'jabatan.required' => 'Jabatan apa hayo? Isi dulu, bro!',
            'jabatan.max' => 'Nama jabatan kepanjangan, maksimal 20 karakter ya!',
            'no_hp.max' => 'Nomor HP kepanjangan, maksimal 15 digit aja, bro!',
            'no_hp.regex' => 'Nomor HP nggak valid, cek lagi formatnya ya!',
            'email.required' => 'Email wajib ada, bro, biar bisa kirim meme!',
            'email.email' => 'Emailnya nggak bener, cek lagi bro!',
            'email.unique' => 'Email ini udah dipake, cari yang lain ya, bro!',
            'password.required' => 'Password jangan kosong, bro, rahasia dong!',
            'password.min' => 'Password minimal 6 karakter, biar aman, bro!',
            'role.required' => 'Role harus dipilih, admin atau pegawai?',
            'foto.image' => 'File harus gambar, bro, jangan file aneh-aneh!',
            'foto.mimes' => 'Foto cuma boleh jpeg, png, atau jpg, bro!',
            'foto.max' => 'Ukuran foto kegedean, maksimal 2MB aja ya!',
        ]);

        try {
            $data = $request->only(['nik', 'nama_lengkap', 'jabatan', 'no_hp', 'email', 'role']);
            $data['password'] = Hash::make($request->password);

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $namaFile = $request->nik . '_' . date('Ymd') . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads/profil', $namaFile, 'public');
                $data['foto'] = $namaFile;
            }

            User::create($data);

            return redirect()->route('data.pegawai')->with('success', 'Yeay! Data pegawai berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('data.pegawai')->with('error', 'Oops! Data gagal disimpan, coba lagi ya! Error: ' . $e->getMessage());
        }
    }

    public function editPegawai(Request $request)
    {
        $nik = $request->nik;
        $pegawai = User::where('nik', $nik)->firstOrFail();
        return view('admin.editpegawai', compact('pegawai'));
    }

    public function updatePegawai(Request $request, $nik)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:20',
            'jabatan' => 'required|string|max:20',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'required|email|max:100|unique:users,email,' . $nik . ',nik',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,pegawai',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama_lengkap.required' => 'Nama lengkap jangan kosong dong!',
            'jabatan.required' => 'Jabatan apa sih? Isi dulu!',
            'email.required' => 'Email wajib diisi, bro!',
            'email.unique' => 'Email ini sudah terdaftar, coba yang lain!',
            'password.min' => 'Password minimal 6 karakter ya!',
            'role.required' => 'Role harus dipilih, admin atau pegawai?',
            'foto.image' => 'File harus gambar (jpeg, png, jpg)!',
            'foto.max' => 'Ukuran foto maksimal 2MB!',
        ]);

        try {
            $pegawai = User::where('nik', $nik)->firstOrFail();
            $data = $request->only(['nama_lengkap', 'jabatan', 'no_hp', 'email', 'role']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('foto')) {
                if ($pegawai->foto && Storage::disk('public')->exists('uploads/profil/' . $pegawai->foto)) {
                    Storage::disk('public')->delete('uploads/profil/' . $pegawai->foto);
                }

                $file = $request->file('foto');
                $namaFile = $nik . '_' . date('Ymd') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/profil', $namaFile, 'public');
                $data['foto'] = $namaFile;
            } else {
                $data['foto'] = $pegawai->getRawOriginal('foto');
            }

            $pegawai->update($data);

            return redirect()->route('data.pegawai')->with('success', 'Hore! Data pegawai berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->route('data.pegawai')->with('error', 'Aduh! Data gagal diupdate, coba lagi ya!');
        }
    }

    public function deletePegawai($nik)
    {
        try {
            $pegawai = User::where('nik', $nik)->firstOrFail();
            
            if ($pegawai->foto && Storage::disk('public')->exists('uploads/profil/' . $pegawai->foto)) {
                Storage::disk('public')->delete('uploads/profil/' . $pegawai->foto);
            }

            $pegawai->delete();

            return redirect()->route('data.pegawai')->with('success', 'Sip! Data pegawai berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('data.pegawai')->with('error', 'Yah! Data gagal dihapus, coba lagi ya!');
        }
    }

    public function konfiglok()
    {
        $konfig = Konfiglok::first();
        return view('admin.konfigurasilokasi', compact('konfig'));
    }

    public function updateKonfiglok(Request $request)
    {
        $konfig = Konfiglok::first();
        $isNewData = !$konfig;

        $request->validate([
            'jamMasuk' => $isNewData ? 'required|date_format:H:i:s' : 'nullable|date_format:H:i:s',
            'jamPulang' => $isNewData ? 'required|date_format:H:i:s' : 'nullable|date_format:H:i:s',
            'lokasiKantor' => $isNewData ? 'required|regex:/^-?\d{1,3}\.\d+,-?\d{1,3}\.\d+$/' : 'nullable|regex:/^-?\d{1,3}\.\d+,-?\d{1,3}\.\d+$/',
            'radius' => $isNewData ? 'required|numeric' : 'nullable|numeric',
        ], [
            'jamMasuk.required' => 'Waduh, jam masuk kok kosong? Isi dong, biar ga telat!',
            'jamMasuk.date_format' => 'Eits, format jam masuk salah tuh! Pakai HH:mm:ss ya, bro!',
            'jamPulang.required' => 'Lho, jam pulang mana? Jangan lupa pulang, nanti dimarahin!',
            'jamPulang.date_format' => 'Aduh, format jam pulang ngaco! Harus HH:mm:ss, sob!',
            'lokasiKantor.required' => 'Lokasi kantor kosong? Kantornya dimana, di Narnia?',
            'lokasiKantor.regex' => 'Aduh, format lokasi kantornya kurang pas! Pakai format: latitude,longitude ya (contoh: -6.200000,106.800000).',
            'radius.required' => 'Radius ga boleh kosong! Mau absen dari luar angkasa?',
            'radius.numeric' => 'Radius harus angka, bro! Bukan puisi cinta!',
        ]);

        try {
            if ($isNewData) {
                $konfig = new Konfiglok();
            }

            if ($request->filled('jamMasuk') && $request->jamMasuk !== '00:00:00') {
                $konfig->jam_masuk_standar = $request->jamMasuk;
            }
            if ($request->filled('jamPulang') && $request->jamPulang !== '00:00:00') {
                $konfig->jam_pulang_standar = $request->jamPulang;
            }
            if ($request->filled('lokasiKantor')) {
                $konfig->lokasi_kantor = $request->lokasiKantor;
            }
            if ($request->filled('radius')) {
                $konfig->radius = $request->radius;
            }

            $konfig->save();

            return redirect()->route('konigurasi.lokasi')->with('success', 'Yuhuu! Konfigurasi berhasil diperbarui, kece abis!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Aduh, gagal update! Ada apa ini: ' . $e->getMessage());
        }
    }

    public function pengajuanIzin(Request $request)
    {
        $query = Izin::with('user')->orderBy('tanggal', 'desc');

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }
        if ($request->filled('nik')) {
            $query->where('nik', 'like', '%' . $request->nik . '%');
        }
        if ($request->filled('nama_lengkap')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
            });
        }
        if ($request->filled('status_approved')) {
            $query->where('status_approval', $request->status_approved);
        }

        $izins = $query->paginate(10);
        $izins->appends($request->all());

        return view('admin.pengajuanizin', compact('izins'));
    }

    public function updatePengajuanizin(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:izins,id',
            'status_approved' => 'required|in:Menunggu,Disetujui,Ditolak',
        ]);

        try {
            $izin = Izin::findOrFail($validated['id']);
            $izin->status_approval = $validated['status_approved'];
            $izin->save();

            return redirect()->back()->with('success', 'Status pengajuan izin berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function batalPengajuanizin($id)
    {
        try {
            $izin = Izin::findOrFail($id);
            $izin->status_approval = 'Menunggu';
            $izin->save();

            return redirect()->back()->with('success', 'Status pengajuan izin berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function deletePengajuanizin($id)
    {
        try {
            $izin = Izin::findOrFail($id);
            $izin->delete();

            return Redirect::back()->with('success', 'Data pengajuan izin berhasil dihapus, semoga tidak menyesal');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Gagal menghapus data pengajuan izin, mungkin datanya belum rela: ' . $e->getMessage());
        }
    }

    public function monitoringAbsen(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $nik = $request->input('nik');
        $nama_lengkap = $request->input('nama_lengkap');
        $query = Absensi::with('user');

        if (!$tanggal && !$bulan && !$tahun && !$nik && !$nama_lengkap) {
            $query->whereDate('tgl_absen', Carbon::today());
        } else {
            if ($tanggal) {
                $query->whereDay('tgl_absen', $tanggal);
            }
            if ($bulan) {
                $query->whereMonth('tgl_absen', $bulan);
            }
            if ($tahun) {
                $query->whereYear('tgl_absen', $tahun);
            }
            if ($nik) {
                $query->where('nik', 'like', '%' . $nik . '%');
            }
            if ($nama_lengkap) {
                $query->whereHas('user', function ($q) use ($nama_lengkap) {
                    $q->where('nama_lengkap', 'like', '%' . $nama_lengkap . '%');
                });
            }
        }

        $absensis = $query->paginate(10);
        $konfig = Konfiglok::first();

        return view('admin.monitoringabsensi', compact('absensis', 'konfig'));
    }

    public function updatemonitoringAbsen(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:absensis,id',
            'tanggal_absen' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i:s',
            'jam_pulang' => 'nullable|date_format:H:i:s',
            'status_masuk' => 'nullable|string|max:255',
            'lokasi_masuk' => 'nullable|string|max:255',
        ]);

        try {
            $absensi = Absensi::findOrFail($validated['id']);
            $absensi->update([
                'tgl_absen' => $validated['tanggal_absen'],
                'jam_masuk' => $validated['jam_masuk'],
                'jam_pulang' => $validated['jam_pulang'],
                'status_masuk' => $validated['status_masuk'],
                'lokasi_masuk' => $validated['lokasi_masuk'],
            ]);

            return Redirect::back()->with('success', 'Data absensi berhasil diupdate tanpa drama');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Update gagal, mungkin server lagi baper: ' . $e->getMessage());
        }
    }

    public function deleteMonitoringAbsen($id)
    {
        try {
            $absensi = Absensi::findOrFail($id);

            if ($absensi->foto_masuk) {
                Storage::delete('public/uploads/absensi/' . $absensi->foto_masuk);
            }
            if ($absensi->foto_pulang) {
                Storage::delete('public/uploads/absensi/' . $absensi->foto_pulang);
            }

            $absensi->delete();

            return Redirect::back()->with('success', 'Data absensi berhasil dihapus, semoga tidak menyesal');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Gagal menghapus data absensi, mungkin datanya belum rela: ' . $e->getMessage());
        }
    }

    public function rekapabsenPegawai()
    {
        $pegawai = User::where('role', 'pegawai')->select('nik', 'nama_lengkap')->get();
        $bulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulan[$i] = Carbon::create()->month($i)->translatedFormat('F');
        }
        $tahun = now()->year;
        $tahunrange = range(2020, $tahun);
        
        return view('admin.rekapitulasipegawai', compact('pegawai', 'bulan', 'tahunrange'));
    }

    public function rekapabsenpegawaiLaporan(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:202',
        ]);

        $pegawai = User::where('nik', $request->nik)->firstOrFail();
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
        $absensis = Absensi::where('nik', $request->nik)
            ->whereMonth('tgl_absen', $bulan)
            ->whereYear('tgl_absen', $tahun)
            ->get()
            ->keyBy('tgl_absen');
        $izins = Izin::where('nik', $request->nik)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_approval', 'Disetujui')
            ->get()
            ->keyBy('tanggal');
        $rekapdata = collect();
        $rekap = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alfa' => 0,
        ];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $tanggal = Carbon::create($tahun, $bulan, $day);
            $tanggalStr = $tanggal->format('Y-m-d');
            $hari = $tanggal->translatedFormat('l');

            if ($tanggal->isWeekend()) {
                continue;
            }

            $absensi = $absensis->get($tanggalStr);
            $izin = $izins->get($tanggalStr);

            if ($absensi && $absensi->jam_masuk) {
                $rekapdata->push((object) [
                    'tanggal' => $tanggal->format('d/m/Y'),
                    'hari' => $hari,
                    'status' => 'Hadir',
                    'jam_masuk' => $absensi->jam_masuk ?? '-',
                    'foto_masuk' => $absensi->foto_masuk
                        ? asset('storage/uploads/absensi/' . $absensi->foto_masuk)
                        : '-',
                    'jam_pulang' => $absensi->jam_pulang ?? '-',
                    'foto_pulang' => $absensi->foto_pulang
                        ? asset('storage/uploads/absensi/' . $absensi->foto_pulang)
                        : '-',
                    'keterangan' => '-',
                ]);
                $rekap['hadir']++;
            } elseif ($izin) {
                $rekapdata->push((object) [
                    'tanggal' => $tanggal->format('d/m/Y'),
                    'hari' => $hari,
                    'status' => $izin->status,
                    'jam_masuk' => '-',
                    'foto_masuk' => '-',
                    'jam_pulang' => '-',
                    'foto_pulang' => '-',
                    'keterangan' => $izin->keterangan,
                ]);
                if ($izin->status === 'Sakit') {
                    $rekap['sakit']++;
                } else {
                    $rekap['izin']++;
                }
            } else {
                $rekapdata->push((object) [
                    'tanggal' => $tanggal->format('d/m/Y'),
                    'hari' => $hari,
                    'status' => 'Alfa',
                    'jam_masuk' => '-',
                    'foto_masuk' => '-',
                    'jam_pulang' => '-',
                    'foto_pulang' => '-',
                    'keterangan' => 'Tanpa Keterangan',
                ]);
                $rekap['alfa']++;
            }
        }

        return view('admin.cetaklaporanpegawai', compact('pegawai', 'bulan', 'tahun', 'rekapdata', 'rekap'));
    }

    public function rekapAbsen()
    {
        $bulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulan[$i] = Carbon::create()->month($i)->translatedFormat('F');
        }
        $tahun = now()->year;
        $tahunrange = range(2020, $tahun);

        return view('admin.rekapitulasiabsen', compact('bulan', 'tahunrange'));
    }

    public function rekapabsenLaporan(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $tanggal = range(1, $jumlahHari);
        $pegawais = User::where('role', 'pegawai')->select('nik', 'nama_lengkap', 'jabatan')->get();
        $absensis = Absensi::whereMonth('tgl_absen', $bulan)
            ->whereYear('tgl_absen', $tahun)
            ->get()
            ->groupBy('nik');
        $izins = Izin::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_approval', 'Disetujui')
            ->get()
            ->groupBy('nik');
        $rekapAbsensi = [];

        foreach ($pegawais as $pegawai) {
            $rekapHarian = [];
            $totalHadir = 0;
            $totalIzin = 0;
            $totalSakit = 0;
            $totalAlpha = 0;

            for ($hari = 1; $hari <= $jumlahHari; $hari++) {
                $tanggalStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $hari);
                $status = 'A';

                if (isset($absensis[$pegawai->nik])) {
                    $absenHariIni = $absensis[$pegawai->nik]->firstWhere('tgl_absen', $tanggalStr);
                    if ($absenHariIni) {
                        $status = 'H';
                        $totalHadir++;
                    }
                }

                if ($status === 'A' && isset($izins[$pegawai->nik])) {
                    $izinHariIni = $izins[$pegawai->nik]->firstWhere('tanggal', $tanggalStr);
                    if ($izinHariIni) {
                        $status = $izinHariIni->status === 'Izin' ? 'I' : 'S';
                        if ($status === 'I') {
                            $totalIzin++;
                        } else {
                            $totalSakit++;
                        }
                    }
                }

                if ($status === 'A') {
                    $totalAlpha++;
                }

                $rekapHarian[$hari] = $status;
            }

            $rekapAbsensi[$pegawai->nik] = [
                'nama_lengkap' => $pegawai->nama_lengkap,
                'jabatan' => $pegawai->jabatan,
                'harian' => $rekapHarian,
                'total' => [
                    'hadir' => $totalHadir,
                    'izin' => $totalIzin,
                    'sakit' => $totalSakit,
                    'alpha' => $totalAlpha,
                ],
            ];
        }

        return view('admin.cetaklaporanabsen', compact('bulan', 'tahun', 'jumlahHari', 'tanggal', 'pegawais', 'rekapAbsensi'));
    }

    public function profilAdmin()
    {
        $user = Auth::user();
        return view('admin.profiladmin', compact('user'));
    }

    public function updateprofileAdmin(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:13',
            'password' => 'nullable|string|min:8',
            'profile-picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi, meow!',
            'jabatan.required' => 'Jabatan harus diisi, apa sih peranmu?',
            'no_hp.max' => 'Nomor HP maksimal 13 digit, jangan kepanjangan ya!',
            'password.min' => 'Password minimal 8 karakter, biar aman meow!',
            'profile-picture.image' => 'File harus gambar, bukan video kucing lucu!',
            'profile-picture.mimes' => 'Gambar harus JPG, JPEG, atau PNG ya!',
            'profile-picture.max' => 'Gambar maksimal 2MB, jangan besar-besar meow!',
        ]);

        $user = Auth::user();
        $nik = $user->nik;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $password = $request->password ? Hash::make($request->password) : null;
        $data = [
            'nama_lengkap' => $nama_lengkap,
            'jabatan' => $jabatan,
            'no_hp' => $no_hp,
        ];

        if ($password) {
            $data['password'] = $password;
        }

        if ($request->hasFile('profile-picture')) {
            if ($user->foto && Storage::disk('public')->exists('uploads/profil/' . $user->foto)) {
                Storage::disk('public')->delete('uploads/profil/' . $user->foto);
            }

            $file = $request->file('profile-picture');
            $namaFile = $nik . '_' . date('Ymd') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/profil', $namaFile, 'public');
            $data['foto'] = $namaFile;
        }

        $update = User::where('nik', $nik)->update($data);

        if ($update) {
            return redirect()->back()->with('success', 'Hore! Profilmu sudah diperbarui, meow!');
        } else {
            return redirect()->back()->with('error', 'Aduh! Gagal update, coba lagi ya, meow!');
        }
    }
}