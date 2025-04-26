<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use App\Models\User;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;

class CheckIfBSU
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 5, // Timeout 5 detik untuk menghindari request yang terlalu lama
        ]);
    }
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if(!$token)
        {
            return $this->errorResponse("Token Authorization Tidak Ditemukan");
        }
        try {
               // Melakukan request ke API untuk mendapatkan profil user
               $response = $this->client->request("GET", env('BSU_BASE_URI')."/api/v1/bsu/profile", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => $token
                ],
            ]);

            $data = json_decode($response->getBody(), true) ?? [];

            if (!isset($data['status'])) {
                return $this->errorResponse("Data user tidak valid atau tidak ditemukan.");
            }
        } catch (RequestException $e) {
            
        }
        return $next($request);
    }

    private function errorResponse(string $message): JsonResponse
    {
        return response()->json([
            "status" => false,
            "message" => $message,
        ], 400);
    }
}
