<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Models\BankSampahUnit;
use App\Models\Users;

class Nasabah extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'nasabah';

    protected $fillable = [
        'id',
        'user_id',
        'nik',
        'nama',
        'alamat',
        'nomor_wa',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'jenis_rekening',
        'reward_level',
        'total_sampah',
        'saldo',
        'bsu_id',
    ];

    protected $casts = [

        'user_id' => 'string',
        'bsu_id' => 'string',
        'id' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankSampahUnit()
    {
        return $this->belongsTo(BankSampahUnit::class, 'bsu_id');
    }

    // public function transaksi()
    // {
    //     return $this->hasMany(TransaksiNasabah::class);
    // }

    // public function insentif()
    // {
    //     return $this->hasMany(InsentifNasabah::class);
    // }
}