<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSKPD extends Model
{
    use HasFactory;

    protected $table = 'master_skpds';

    protected $fillable = [
        'kode_skpd',
        'nama_skpd',
        'alamat',
        'telepon',
        'email',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function knowledge()
    {
        return $this->hasMany(Knowledge::class, 'skpd_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'skpd_id');
    }
}
