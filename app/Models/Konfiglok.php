<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konfiglok extends Model
{
    use HasFactory;

    protected $table = 'konfigloks';

    protected $fillable = [
        'lokasi_kantor', // Format: "latitude,longitude"
        'radius', // Dalam meter
        'jam_masuk_standar',
        'jam_pulang_standar',
    ];
}