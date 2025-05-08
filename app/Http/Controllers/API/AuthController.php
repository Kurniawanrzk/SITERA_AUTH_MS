<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:nasabah,bsu,perusahaan,pemerintah',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // Buat user
        $user = User::create([
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ]);


        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Autentikasi dasar
        if (!$token = auth()->setTTL(7200)->attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }
    
        $user = auth()->user();
        
        // Inisialisasi client HTTP
        $client = new \GuzzleHttp\Client([
            'timeout' => 5 // Set timeout pendek untuk mencegah blocking
        ]);
        
        // Inisialisasi variable role
        $roleData = [
            'is_bsu' => false,
            'bsu_id' => null,
            'is_nasabah' => false,
            'nasabah_id' => null,
            'is_perusahaan' => false,
            'perusahaan_id' => null,
            'is_pemerintah' => false,
            'pemerintah_id' => null
        ];
        
        // Array endpoint untuk masing-masing role
        $endpoints = [
            'bsu' => [
                'uri' => 'http://145.79.10.111:8003/api/v1/bsu/cek-user/' . $user->id,
                'is_key' => 'is_bsu',
                'id_key' => 'bsu_id'
            ],
            'nasabah' => [
                'uri' => 'http://145.79.10.111:8004/api/v1/nasabah/cek-user/' . $user->id,
                'is_key' => 'is_nasabah',
                'id_key' => 'nasabah_id'
            ],
            'pemerintah' => [
                'uri' => 'http://145.79.10.111:8005/api/v1/pemerintah/cek-user/' . $user->id,
                'is_key' => 'is_pemerintah',
                'id_key' => 'pemerintah_id'
            ]/*
            'perusahaan' => [
                'uri' => env('PERUSAHAAN_BASE_URI') . '/api/v1/perusahaan/check-user/' . $user->id,
                'is_key' => 'is_perusahaan',
                'id_key' => 'perusahaan_id'
            ],
            */
        ];

        
        // Cek setiap role
        foreach ($endpoints as $role => $endpoint) {
            
            try {
                $response = $client->request('GET', $endpoint['uri'], [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                ]);
                
                $data = json_decode($response->getBody(), true);
                $isRole = isset($data['status']) && $data['status'] === true;
                $roleId = $isRole ? $data['data']['id'] ?? null : null;
                
                $roleData[$endpoint['is_key']] = $isRole;
                $roleData[$endpoint['id_key']] = $roleId;
                
            } catch (\Exception $e) {
                // Log error jika perlu
                \Log::error("Failed to check {$role} role: " . $e->getMessage());
                // Tidak perlu mengubah nilai default (false/null)
            }
        }
        
        // Tambahkan custom claims ke token
        $customClaims = $roleData;
        
        // Buat token baru dengan custom claims
        $newToken = auth()->claims($customClaims)->setTTL(7200)->fromUser($user);
        
        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'roles' => $roleData
            ]
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
}

    public function profile(Request $request)
    {
        $user = auth()->user();
        if(isset($user)) {
            return response()->json([
                'status' => true,
                'data' => [
                    'user' => $user,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false
            ], 40);
        }
        
    }

    public function editProfile(Request $request)
    {
        $user = Auth::user();

        if(isset($request->email)) 
        {
            $user->email = $request->email;
        }
        if(isset($request->password))
        {
            $user->password = Hash::make($request->password);
        }

        if(isset($request->password) || isset($request->email))
        {
            $user->save();
        }
        

        return response()
        ->json([
            "status" => true,
            "message" => "Berhasil Di Edit"
        ], 200);
    }

    public function cekToken(Request $request)
    {
        try{
            $bearer_token = $request->header("Authorization");

            $token = str_replace('Bearer ', '', $bearer_token);

            if(!auth()->setToken($token)->check())
            {
                return response()
                ->json([
                    "status" => false,
                    "message" => "Token invalid!"
                ], 401);
            }


            $payload = auth()->setToken($token)->payload();
            $user = auth()->setToken($token)->user();

            $roles = [
                'is_bsu' => $payload->get('is_bsu') ?? false,
                'bsu_id' => $payload->get('bsu_id'),
                'is_nasabah' => $payload->get('is_nasabah') ?? false,
                'nasabah_id' => $payload->get('nasabah_id'),
                'is_perusahaan' => $payload->get('is_perusahaan') ?? false,
                'perusahaan_id' => $payload->get('perusahaan_id'),
                'is_pemerintah' => $payload->get('is_pemerintah') ?? false,
                'pemerintah_id' => $payload->get('pemerintah_id')
            ];

            return response()
            ->json([
                'status' => true,
                'message' => "Token valid!",
                'data' => [
                        "user" => $user,
                        "roles" => $roles                                             
                ]
            ], 200);


        }catch(\Exception $e)
        {
            return response()->json([
                'status' => false,
                "message" => "gagal memverifikasi token :" . $e->getMessage()
            ], 500);
        }
    }

    public function buatAkunNasabah(Request $request)
    {
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => "nasabah",
        ]);

        $token = auth()->login($user);
        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() 
            ]
        ], 201);
    }

}