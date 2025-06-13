<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Izin;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Konfiglok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function dashboard()
    {
        $nik = Auth::user()->nik;
        $today = Carbon::today()->format('Y-m-d');
        $currentMonth = Carbon::now()->translatedFormat('F');
        $currentYear = Carbon::now()->year;

        $konfiglok = Konfiglok::first();
        
        $jamMasuk = $konfiglok ? $konfiglok->jam_masuk_standar : '00:00:00';
        $jamPulang = $konfiglok ? $konfiglok->jam_pulang_standar : '00:00:00';

        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        $hadir = Absensi::where('nik', $nik)
            ->whereBetween('tgl_absen', [$startOfMonth, $endOfMonth])
            ->whereNotNull('jam_masuk')
            ->count();

        $terlambat = Absensi::where('nik', $nik)
            ->whereBetween('tgl_absen', [$startOfMonth, $endOfMonth])
            ->where('status_masuk', 'Terlambat')
            ->count();

        $izin = Izin::where('nik', $nik)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('status', 'Izin')
            ->where('status_approval', 'Disetujui')
            ->count();

        $sakit = Izin::where('nik', $nik)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('status', 'Sakit')
            ->where('status_approval', 'Disetujui')
            ->count();

        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');
        $absensiMingguIni = Absensi::where('nik', $nik)
            ->whereBetween('tgl_absen', [$startOfWeek, $endOfWeek])
            ->orderBy('tgl_absen', 'asc')
            ->get();

        $leaderboard = Absensi::with('user')
            ->where('tgl_absen', $today)
            ->orderBy('jam_masuk', 'asc')
            ->get();

        return view('pegawai.dashboard', compact('jamMasuk', 'jamPulang', 'hadir', 'izin', 'sakit', 'terlambat', 'absensiMingguIni', 'leaderboard', 'currentMonth', 'currentYear'));
    }

    public function absen()
    {
        $hariini = Carbon::today()->format('Y-m-d');
        $nik = Auth::user()->nik;
        $cek = Absensi::where('tgl_absen', $hariini)->where('nik', $nik)->count();
        $lok_kantor = Konfiglok::first();

        return view('pegawai.absen', compact('cek', 'lok_kantor'));
    }

    public function storeAbsen(Request $request)
    {
        $nik = Auth::user()->nik;
        $tgl_absen = Carbon::today()->format('Y-m-d');
        $jam = Carbon::now()->format('H:i:s');
        $jam_sekarang = Carbon::now();

        $lok_kantor = Konfiglok::first();
        if (!$lok_kantor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konfigurasi lokasi belum diatur! Hubungi admin dulu, bro!',
                'type' => 'config'
            ]);
        }

        $jam_masuk_standar = Carbon::createFromFormat('H:i:s', $lok_kantor->jam_masuk_standar);
        $jam_pulang_standar = Carbon::createFromFormat('H:i:s', $lok_kantor->jam_pulang_standar);

        $jam_mulai_masuk = Carbon::createFromTime(5, 0, 0);
        $jam_akhir_masuk = $jam_pulang_standar->copy()->subSecond();
        $jam_batas_tepat_waktu = $jam_masuk_standar;

        $cekIzin = Izin::where('nik', $nik)
            ->where('tanggal', $tgl_absen)
            ->exists();

        if ($cekIzin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Maaf, kamu sudah mengajukan izin untuk hari ini. Tidak bisa absen!',
                'type' => 'in'
            ]);
        }

        $lok = explode(",", $lok_kantor->lokasi_kantor);
        $latitudekantor = $lok[0];
        $longitudekantor = $lok[1];
        $lokasi = $request->lokasi;
        if (empty($lokasi) || substr_count($lokasi, ',') < 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Peta kamu masih loading kayak WiFi tetangga! Tunggu bentar, baru absen, bro!',
                'type' => 'location'
            ]);
        }
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        if ($radius > $lok_kantor->radius) {
            return response()->json([
                'status' => 'error',
                'message' => 'Waduh, kamu absen dari luar angkasa ya? Jarakmu ' . $radius . ' meter dari kantor!',
                'type' => 'radius'
            ]);
        }

        $cek = Absensi::where('tgl_absen', $tgl_absen)->where('nik', $nik)->count();

        $image = $request->image;
        $folderPath = "Uploads/absensi/";
        $formatName = $nik . "-" . $tgl_absen . "-" . ($cek > 0 ? 'pulang' : 'masuk');
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if ($cek > 0) {
            if ($jam_sekarang->lt($jam_pulang_standar)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sabar bro, pulangnya nanti setelah jam ' . $jam_pulang_standar->format('H:i') . ', sekarang lanjutin kerja dulu!',
                    'type' => 'out'
                ]);
            }

            $data_pulang = [
                'jam_pulang' => $jam,
                'foto_pulang' => $fileName,
                'lokasi_pulang' => $lokasi,
            ];
            $update = Absensi::where('tgl_absen', $tgl_absen)->where('nik', $nik)->update($data_pulang);
            if ($update) {
                Storage::disk('public')->put($file, $image_base64);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Yeay, pulang dulu ya! Hati-hati di jalan, jangan lupa bawa dompet!',
                    'type' => 'out'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aduh, absen pulang gagal! Apa jaringannya lagi ngambek ya?',
                    'type' => 'out'
                ]);
            }
        } else {
            if ($jam_sekarang->lt($jam_mulai_masuk) || $jam_sekarang->gt($jam_akhir_masuk)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Halo bro, absen masuk cuma boleh dari jam 05:00 sampai sebelum ' . $jam_akhir_masuk->format('H:i') . ', ini kamu absen dari zona waktu lain apa gimana?!',
                    'type' => 'in'
                ]);
            }

            $status_masuk = $jam_sekarang->lte($jam_batas_tepat_waktu) ? 'Tepat Waktu' : 'Terlambat';

            $data = [
                'nik' => $nik,
                'tgl_absen' => $tgl_absen,
                'jam_masuk' => $jam,
                'foto_masuk' => $fileName,
                'lokasi_masuk' => $lokasi,
                'status_masuk' => $status_masuk,
            ];
            $simpan = Absensi::create($data);
            if ($simpan) {
                Storage::disk('public')->put($file, $image_base64);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Mantap, absen masuk berhasil! Semangat kerja, jangan lupa ngopi biar melek!',
                    'type' => 'in'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Oops, absen masuk gagal! Apa kameranya lupa senyum tadi?',
                    'type' => 'in'
                ]);
            }
        }
    }
    
    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $meters = $miles * 1609.344;
        return ["meters" => $meters];
    }

    public function izin()
    {
        $nik = Auth::user()->nik;
        
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        $izin = Izin::where('nik', $nik)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->orderBy('tanggal', 'desc')
            ->paginate(6)->withQueryString();

        return view('pegawai.izin', compact('izin'));
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'tanggal_izin' => 'required|date|after_or_equal:today',
            'status' => 'required|in:Izin,Sakit',
            'alasan' => 'required|string|max:255',
        ], [
            'tanggal_izin.required' => 'Tanggal izin wajib diisi, bro!',
            'tanggal_izin.date' => 'Tanggal izin harus format tanggal yang valid!',
            'tanggal_izin.after_or_equal' => 'Tanggal izin tidak boleh kemarin, masa lalu biar berlalu!',
            'status.required' => 'Status izin harus dipilih, izin atau sakit nih?',
            'status.in' => 'Status cuma boleh Izin atau Sakit, jangan ngarang ya!',
            'alasan.required' => 'Alasan wajib diisi, kasih tahu kenapa dong!',
            'alasan.max' => 'Alasan maksimal 255 karakter, singkat aja bro!',
        ]);

        $nik = Auth::user()->nik;
        $tanggal = Carbon::parse($request->tanggal_izin)->format('Y-m-d');
        $today = Carbon::today()->format('Y-m-d');
        $status = $request->status;
        $keterangan = $request->alasan;

        if ($tanggal !== $today) {
            return redirect()->back()->with('error', 'Maaf, izin hanya bisa diajukan untuk hari ini, bukan tanggal ke depan!');
        }

        $cekAbsensi = Absensi::where('nik', $nik)
            ->where('tgl_absen', $tanggal)
            ->exists();

        if ($cekAbsensi) {
            return redirect()->back()->with('error', 'Ups, kamu sudah absen untuk tanggal ini! Tidak bisa ajukan izin.');
        }

        $cekIzin = Izin::where('nik', $nik)
            ->where('tanggal', $tanggal)
            ->exists();

        if ($cekIzin) {
            return redirect()->back()->with('error', 'Ups, kamu sudah ajukan izin untuk tanggal ini!');
        }

        $simpan = Izin::create([
            'nik' => $nik,
            'tanggal' => $tanggal,
            'status' => $status,
            'keterangan' => $keterangan,
        ]);

        if ($simpan) {
            return redirect()->back()->with('success', 'Hore, pengajuan izin berhasil! Tunggu konfirmasi ya, bro!');
        } else {
            return redirect()->back()->with('error', 'Aduh, gagal simpan izin! Coba lagi nanti ya!');
        }
    }

    public function histori(Request $request)
    {
        $user = Auth::user();
        $histori = collect();
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        if ($bulan && $tahun) {
            $request->validate([
                'bulan' => 'required|integer|between:1,12',
                'tahun' => 'required|integer|digits:4',
            ]);

            $absensi = Absensi::where('nik', $user->nik)
                ->whereMonth('tgl_absen', $bulan)
                ->whereYear('tgl_absen', $tahun)
                ->orderBy('tgl_absen', 'desc')
                ->get()
                ->keyBy('tgl_absen');

            $izin = Izin::where('nik', $user->nik)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get()
                ->keyBy('tanggal');

            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate = ($bulan == now()->month && $tahun == now()->year)
                ? now()
                : $startDate->copy()->endOfMonth();

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $tanggal = $currentDate->toDateString();

                $record = new \stdClass();
                $record->tgl_absen = $tanggal;
                $record->jam_masuk = '-';
                $record->jam_pulang = '-';
                $record->status = 'Tidak Absen';

                if ($izin->has($tanggal)) {
                    $record->status = $izin[$tanggal]->status;
                } elseif ($absensi->has($tanggal)) {
                    $absenHariIni = $absensi[$tanggal];
                    $record->jam_masuk = $absenHariIni->jam_masuk ?? '-';
                    $record->jam_pulang = $absenHariIni->jam_pulang ?? '-';

                    if ($absenHariIni->jam_masuk) {
                        $jamMasuk = Carbon::parse($absenHariIni->jam_masuk);
                        $batasWaktu = Carbon::parse($absenHariIni->jam_masuk)->setTime(8, 0, 0);

                        $record->status = $jamMasuk->lessThanOrEqualTo($batasWaktu)
                            ? 'Tepat Waktu'
                            : 'Terlambat';
                    } elseif ($absenHariIni->status_masuk) {
                        $record->status = $absenHariIni->status_masuk;
                    }
                }

                $histori->push($record);
                $currentDate->addDay();
            }
        }

        return view('pegawai.histori', compact('histori', 'bulan', 'tahun'));
    }
    
    public function profil()
    {
        $user = Auth::user();
        return view('pegawai.profil', compact('user'));
    }

    public function updateProfile(Request $request)
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

            if ($file->storeAs('uploads/profil', $namaFile, 'public')) {
                $data['foto'] = $namaFile;
            } else {
                return redirect()->back()->with('error', 'Gagal menyimpan foto, coba lagi meow!');
            }
        }

        $update = User::where('nik', $nik)->update($data);

        if ($update) {
            return redirect()->back()->with('success', 'Hore! Profilmu sudah diperbarui, meow!');
        } else {
            return redirect()->back()->with('error', 'Aduh! Gagal update, coba lagi ya, meow!');
        }
    }
}