<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

    protected $table = 'izins';

    protected $fillable = [
        'nik',
        'tanggal',
        'status',
        'keterangan',
        'status_approval',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }
    
}