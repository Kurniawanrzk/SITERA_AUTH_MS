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
        $inactiveBSU = User::where('role', 'bsu')->where('status_acc', 'inactive')->get();
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
                ]);
            }
        }
    
        // Ambil user Perusahaan yang tidak aktif
        $inactivePerusahaan = User::where('role', 'perusahaan')->where('status_acc', 'inactive')->get();
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
    

    public function updateStatusUser(Request $request, $userId)
    {
        // Temukan pengguna berdasarkan ID
        $user = User::find($userId);
        $pesan = "";
        $pesanStatus = false;
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak ditemukan',
            ], 404);
        }
        if($request->status == 'active'){
            $user->status_acc = 'active';
            $pesan = "Pengguna berhasil diaktifkan";
            $pesanStatus = true;
        }else{
            $user->status_acc = 'inactive';
            $pesan = "Pengguna berhasil dinonaktifkan";
        }
        // Simpan perubahan status pengguna
        $user->save();

        return response()->json([
            'status' => $pesanStatus,
            'message' => $pesan,
            'data' => $user,
        ], 200);
    }

    
}
