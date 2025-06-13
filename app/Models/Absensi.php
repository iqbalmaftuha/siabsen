<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'nik',
        'tgl_absen',
        'jam_masuk',
        'jam_pulang',
        'foto_masuk',
        'foto_pulang',
        'lokasi_masuk',
        'lokasi_pulang',
        'status_masuk',
    ];

    // protected $casts = [
    //     'tgl_absen' => 'date:d-m-Y',
    //     'jam_masuk' => 'datetime:H:i:s',
    //     'jam_pulang' => 'datetime:H:i:s',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }

    public function scopeByMonthYear($query, $bulan, $tahun)
    {
        return $query->whereMonth('tgl_absen', $bulan)
                     ->whereYear('tgl_absen', $tahun);
    }
    
}
