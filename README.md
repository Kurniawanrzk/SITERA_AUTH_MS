# Dokumentasi API - Microservices Autentikasi

## Pendahuluan
Dokumentasi ini menjelaskan endpoint yang tersedia dalam layanan autentikasi microservices. API ini digunakan untuk proses registrasi, login, logout, dan mendapatkan profil pengguna.

## Basis URL
```
http://your-api-domain.com/api
```

---

## 1. Registrasi Pengguna
### Endpoint
```
POST /register
```
### Deskripsi
Endpoint ini digunakan untuk mendaftarkan pengguna baru berdasarkan perannya (nasabah, bsu, perusahaan, pemerintah).

### Parameter Request
| Nama            | Tipe   | Wajib | Deskripsi |
|---------------|--------|------|------------|
| email         | string | Ya   | Email pengguna unik |
| password      | string | Ya   | Minimal 8 karakter |
| role          | string | Ya   | Peran pengguna (`nasabah`, `bsu`, `perusahaan`, `pemerintah`) |
| nomor_registrasi (bsu) | string | Ya* | Nomor registrasi unik untuk BSU |
| nama_bsu (bsu) | string | Ya* | Nama Bank Sampah Unit |
| nama_perusahaan (perusahaan) | string | Ya* | Nama perusahaan |
| jenis_perusahaan (perusahaan) | string | Ya* | Jenis perusahaan |
| kode_instansi (pemerintah) | string | Ya* | Kode unik instansi pemerintah |
| nama_instansi (pemerintah) | string | Ya* | Nama instansi pemerintah |

(*) Wajib sesuai dengan peran yang dipilih.

### Contoh Request
```json
{
    "email": "user@example.com",
    "password": "password123",
    "role": "bsu",
    "nomor_registrasi": "BSU-001",
    "nama_bsu": "Bank Sampah Sejahtera"
}
```

### Contoh Respons Berhasil
```json
{
    "status": true,
    "message": "Registrasi berhasil",
    "data": {
        "user": { "id": 1, "email": "user@example.com", "role": "bsu" },
        "token": "eyJhbGciOiJIUzI1NiIsIn...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

### Contoh Respons Gagal
```json
{
    "status": false,
    "message": "Validasi gagal",
    "errors": {
        "email": ["Email sudah digunakan"]
    }
}
```

---

## 2. Login Pengguna
### Endpoint
```
POST /login
```
### Deskripsi
Endpoint ini digunakan untuk proses login pengguna.

### Parameter Request
| Nama      | Tipe   | Wajib | Deskripsi |
|----------|--------|------|------------|
| email    | string | Ya   | Email pengguna |
| password | string | Ya   | Kata sandi pengguna |

### Contoh Request
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

### Contoh Respons Berhasil
```json
{
    "status": true,
    "message": "Login berhasil",
    "data": {
        "user": { "id": 1, "email": "user@example.com", "role": "bsu" },
        "profile": { "nama_bsu": "Bank Sampah Sejahtera" },
        "token": "eyJhbGciOiJIUzI1NiIsIn...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

### Contoh Respons Gagal
```json
{
    "status": false,
    "message": "Email atau password salah"
}
```

---

## 3. Logout Pengguna
### Endpoint
```
POST /logout
```
### Deskripsi
Endpoint ini digunakan untuk logout pengguna dan menghapus token autentikasi.

### Header Authorization
| Nama            | Tipe   | Wajib | Deskripsi |
|---------------|--------|------|------------|
| Authorization | string | Ya   | Token Bearer pengguna |

### Contoh Request
```
Header:
Authorization: Bearer {token}
```

### Contoh Respons Berhasil
```json
{
    "status": true,
    "message": "Logout berhasil"
}
```

---

## 4. Mendapatkan Profil Pengguna
### Endpoint
```
GET /profile
```
### Deskripsi
Endpoint ini digunakan untuk mendapatkan informasi akun pengguna yang sedang login.

### Header Authorization
| Nama            | Tipe   | Wajib | Deskripsi |
|---------------|--------|------|------------|
| Authorization | string | Ya   | Token Bearer pengguna |

### Contoh Request
```
Header:
Authorization: Bearer {token}
```

### Contoh Respons Berhasil
```json
{
    "status": true,
    "data": {
        "user": { "id": 1, "email": "user@example.com", "role": "bsu" },
        "profile": { "nama_bsu": "Bank Sampah Sejahtera" }
    }
}
```

---

## Kesimpulan
Dokumentasi ini mencakup API autentikasi dengan fitur registrasi, login, logout, dan profil pengguna. Pastikan setiap permintaan yang membutuhkan autentikasi menggunakan token Bearer yang valid.

