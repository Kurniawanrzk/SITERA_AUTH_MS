<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaan';

    protected $fillable = [
        'id',
        'user_id',
        // 'nib_npwp',
        'nama_perusahaan',
        'jenis_perusahaan',
        'alamat',
        'longitude',
        'latitude',
        'email_bisnis',
        'nomor_telepon',
        'gambar_perusahaan',
    ];

    protected $casts = [

        'user_id' => 'string',
        'id' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function transaksiPihakKetiga()
    // {
    //     return $this->hasMany(TransaksiPihakKetiga::class, 'perusahaan_id');
    // }
}