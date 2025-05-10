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
        $inactiveBSU = User::where('role', 'bsu')->where('status_acc', 'inactive')
        ->get('id');

        $user_ids = $inactiveBSU->pluck('id')->toArray();
        $response_bsu = $client->request("GET", "http://145.79.10.111:8003/api/v1/bsu/cek-bsu-superadmin", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $request->header('Authorization'),
            ],
            'json' => [
                'user_ids' => $user_ids,
            ],
        ]);
        $data_bsu = json_decode($response_bsu->getBody(), true) ?? [];
        if (!isset($data_bsu['status'])) {
            return response()->json([
                'status' => false,
                'message' => 'Data BSU tidak valid atau tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Daftar pengguna tidak aktif',
            'data' => [
                'bsu' => $data_bsu['data'],
            ],
        ]);
        $inactivePerusahaan = User::where('role', 'perusahaan')->where('status_acc', 'inactive')->get('id');

        // Gabungkan hasil
        return response()->json([
            'status' => true,
            'message' => 'Daftar pengguna tidak aktif',
            'data' => [
                'bsu' => $inactiveBSU,
                'perusahaan' => $inactivePerusahaan,
            ],
        ]);
    }

    public function activateUser(Request $request, $userId)
    {
        // Temukan pengguna berdasarkan ID
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak ditemukan',
            ], 404);
        }

        // Perbarui status pengguna menjadi aktif
        $user->status_acc = 'active';
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Pengguna berhasil diaktifkan',
            'data' => $user,
        ]);
    }

    
}
