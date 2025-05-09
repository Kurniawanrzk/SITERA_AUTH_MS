<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
class SuperadminController extends Controller
{
    public function getInactiveUsers(Request $request)
    {
        $client = new Client();
    
        // Ambil user BSU yang tidak aktif
        $inactiveBSU = User::where('role', 'bsu')->get();
        $user_ids_bsu = $inactiveBSU->pluck('id')->toArray();
    
        // Panggil API BSU
        $response_bsu = $client->request("GET", "http://145.79.10.111:8003/api/v1/bsu/cek-bsu-superadmin", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $request->header('Authorization'),
            ],
            'json' => [
                'user_ids' => $user_ids_bsu,
            ],
        ]);
        $data_bsu = json_decode($response_bsu->getBody(), true) ?? [];
    
        if (!isset($data_bsu['status'])) {
            return response()->json([
                'status' => false,
                'message' => 'Data BSU tidak valid atau tidak ditemukan.',
            ], 404);
        }
    
        // Gabungkan data BSU API dengan data user
        $bsu_users = [];
        foreach ($data_bsu['data'] as $bsu) {
            $user = $inactiveBSU->firstWhere('id', $bsu['user_id']);
            if ($user) {
                $bsu_users[] = array_merge($bsu, [
                    'email' => $user->email,
                    'role' => $user->role,
                    'id' => $user->id,
                    'status' => $user->status_acc,
                ]);
            }
        }
    
        // Ambil user Perusahaan yang tidak aktif
        $inactivePerusahaan = User::where('role', 'perusahaan')->get();
        $user_ids_perusahaan = $inactivePerusahaan->pluck('id')->toArray();
    
        // Panggil API Perusahaan
        $response_perusahaan = $client->request("GET", "http://145.79.10.111:8006/api/v1/perusahaan/cek-perusahaan-superadmin", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $request->header('Authorization'),
            ],
            'json' => [
                'user_ids' => $user_ids_perusahaan,
            ],
        ]);
        $data_perusahaan = json_decode($response_perusahaan->getBody(), true) ?? [];
    
        if (!isset($data_perusahaan['status'])) {
            return response()->json([
                'status' => false,
                'message' => 'Data Perusahaan tidak valid atau tidak ditemukan.',
            ], 404);
        }
    
        // Gabungkan data Perusahaan API dengan data user
        $perusahaan_users = [];
        foreach ($data_perusahaan['data'] as $perusahaan) {
            $user = $inactivePerusahaan->firstWhere('id', $perusahaan['user_id']);
            if ($user) {
                $perusahaan_users[] = array_merge($perusahaan, [
                    'email' => $user->email,
                    'role' => $user->role,
                    'id' => $user->id,
                    'status' => $user->status_acc,
                ]);
            }
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Daftar pengguna tidak aktif',
            'data' => [
                'bsu' => $bsu_users,
                'perusahaan' => $perusahaan_users,
            ],
        ]);
    }
    

    public function updateStatusUser(Request $request, $userId, $nomorTelepon,$status, $alasanDiTolak = null)
    {
        // Temukan pengguna berdasarkan ID
        $user = User::find($userId);
        $pesan = "";
        $pesanWA = "";
        $client = new Client();
        $pesanStatus = false;
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak ditemukan',
            ], 404);
        }
        if($status == 'approve'){
            $user->status_acc = 'active';
            $pesan = "Pengguna berhasil diaktifkan";
            $pesanStatus = true;
            $pesanWA = "Halo Bapak/Ibu,

Selamat! Akun Anda telah berhasil diaktifkan di platform SITERA.

Anda sekarang dapat mengakses seluruh fitur dengan mengunjungi situs resmi kami di:
sitera.site

Silakan login menggunakan kredensial yang telah Anda daftarkan sebelumnya. Jika Anda memerlukan bantuan lebih lanjut atau mengalami kendala saat login, jangan ragu untuk menghubungi tim dukungan kami.

Terima kasih telah memilih SITERA sebagai mitra digital Anda.

Salam,
Tim SITERA";
        }else{
            $user->status_acc = 'inactive';
            $pesan = "Pengguna berhasil dinonaktifkan";
            $pesanStatus = false;
            $pesanWA = "Halo Bapak/Ibu,

Mohon maaf, kami ingin menginformasikan bahwa proses aktivasi akun Anda di platform SITERA belum dapat diselesaikan karena alasan berikut:

*{$alasanDiTolak}*

Jika Anda memiliki pertanyaan mendesak, silakan balas pesan ini atau hubungi tim dukungan kami di [nomor/email dukungan].

Terima kasih atas pengertian Anda.

Salam,
Tim SITERA";
        }
        // Simpan perubahan status pengguna
        $user->save();

        try {
            $response = $client->request('POST', 'https://api.fonnte.com/send', [
                'headers' => [
                    'Authorization' => 'vcnjUGCjCxauEgdqBPgp', // ganti TOKEN dengan token asli
                ],
                'form_params' => [
                    'target' => $nomorTelepon,
                    'message' => $pesanWA,
                    'countryCode' => '62', // optional
                ],
            ]);
        
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                echo $e->getResponse()->getBody();
            } else {
                echo $e->getMessage();
            }
        }

        return response()->json([
            'status' => $pesanStatus,
            'message' => $pesan,
            'data' => $user,
        ], 200);
    }

    
}
