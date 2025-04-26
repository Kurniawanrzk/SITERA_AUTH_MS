<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class Pemerintah extends Model
{
    use HasFactory;

    protected $table = 'pemerintah';

    protected $fillable = [
        'user_id',
        'kode_instansi',
        'nama_instansi',
        'jabatan',
        'nama_pejabat',
        'alamat',
        'email_instansi',
        'nomor_telepon',
    ];

    protected $casts = [

        'user_id' => 'string',
        'id' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
