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
        ->get();
        return response()->json([
            'status' => true,
            'message' => 'Daftar pengguna tidak aktif',
            'data' => $inactiveBSU,
        ]);
        $inactivePerusahaan = User::where('role', 'perusahaan')->where('status_acc', 'inactive')->get();

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
