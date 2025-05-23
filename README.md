# 🚀 SI-KRS Frontend - Laravel + Backend CodeIgniter
Ini adalah proyek antarmuka pengguna (frontend) berbasis Laravel 10 dan Tailwind CSS yang dirancang untuk terhubung dengan backend REST API (dibangun dengan CodeIgniter 4). Aplikasi ini digunakan untuk mengelola data Mahasiswa, Program Studi (Prodi), dan Kelas.

- [Backend SINilai Github](https://github.com/Arfilal/backend_sinilai.git)
- [Database SINilai Github](https://github.com/HanaKurnia/database_pbf.git)

# ⚙ Teknologi
- Laravel 10
- Tailwind CSS
- Laravel HTTP Client (untuk konsumsi API)
- Vite (build asset frontend)
- REST API (CodeIgniter 4)

# 🧩 Struktur Sistem
Frontend Laravel ini tidak menyimpan data ke database lokal. Semua proses Create, Read, Update, dan Delete dilakukan melalui REST API backend CodeIgniter.

# 🚀 SETUP BACKEND
1. Clone Repository BE
```
git clone https://github.com/Arfilal/backend_sinilai.git
```
```
cd nama-file
```
2. Install Dependency CodeIgniter
``
composer install
``
3. Copy File Environment
```
cp .env.example .env
```
4. Menjalankan CodeIgniter
```
php spark serve
```
5. Cek EndPoint menggunakan Postman
- Mahasiswa
```
GET http://localhost:8080/mahasiswa
POST http://localhost:8080/mahasiswa/{npm}
DEL http://localhost:8080/mahasiswa/{npm}
PUT http://localhost:8080/mahasiswa/{npm}
```

- Mata Kuliah
```
GET http://localhost:8080/matakuliah
POST http://localhost:8080/matakuliah/{kode_matkul}
DEL http://localhost:8080/matakuliah/{kode_matkul}
PUT http://localhost:8080/matakuliah/{kode_matkul}
```


# 🚀 SETUP FRONTEND
1. Install Laravel
Install di CMD atau Terminal
```
composer create-priject laravel/laravel nama-project
```

2. Install Dependency Laravel
```
composer install
```
3. Copy File Environment
```
cp .env.example .env
```
4. Set .env untuk Non-Database App
```
APP_NAME=Laravel
APP_URL=http://localhost:8000
SESSION_DRIVER=file
```

5. Cara Menjalankan Laravel server
```
php artisan serve
```

## 🧩  Routes
```
<?php

use App\Controllers\MataKuliah;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\ProdiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DashboardController::class, 'index'])->name('Dashboard.index');

Route::resource('Mahasiswa', MahasiswaController::class);
Route::resource('MataKuliah', MataKuliahController::class);

//Route::get('/export-pdf', [CetakKHSController::class, 'exportPdf'])->name('export.pdf');
Route::get('/export-pdf', [MataKuliahController::class, 'exportPdf'])->name('export.pdf');
```

## 🧩  Controllers
1. MahasiswaController.php
```
<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $response = Http::get('http://localhost:8080/mahasiswa');


        if ($response->successful()) {
            $mahasiswa = collect($response->json())->sortBy('npm')->values();

            return view('Mahasiswa', compact('mahasiswa'));
        } else {
            return back()->with('error', 'Gagal mengambil data mahasiswa');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        //
        $respon_kelas = Http::get('http://localhost:8080/matakuliah');
        $kelas = collect($respon_kelas->json())->sortBy('kode_matkul')->values();

        $respon_prodi = Http::get('http://localhost:8080/prodi');
        $prodi = collect($respon_prodi->json())->sortBy('kode_prodi')->values();

        return view('tambahmahasiswa', [
            'kelas' => $kelas,
            'prodi' => $prodi
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $validate = $request->validate([
                'npm' => 'required',
                'nama_mahasiswa' => 'required',
                'id_kelas' => 'required',
                'kode_prodi' => 'required'
            ]);

            Http::asForm()->post('http://localhost:8080/mahasiswa', $validate);

            return redirect()->route('Mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($mahasiswa)
    {
        //
        $mahasiswaResponse = Http::get("http://localhost:8080/mahasiswa/$mahasiswa");
        $kelas = Http::get("http://localhost:8080/kelas")->json();
        $prodi = Http::get("http://localhost:8080/prodi")->json();

        if ($mahasiswaResponse->successful() && !empty($mahasiswaResponse[0])) {
            $mahasiswa = $mahasiswaResponse[0];

            // Tambahkan pencocokan manual ID berdasarkan nama
            foreach ($kelas as $k) {
                if ($k['nama_kelas'] === $mahasiswa['nama_kelas']) {
                    $mahasiswa['id_kelas'] = $k['id_kelas'];
                    break;
                }
            }

            foreach ($prodi as $p) {
                if ($p['nama_prodi'] === $mahasiswa['nama_prodi']) {
                    $mahasiswa['kode_prodi'] = $p['kode_prodi'];
                    break;
                }
            }

            return view('editmahasiswa', compact('mahasiswa', 'kelas', 'prodi'));
        } else {
            return back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $mahasiswa)
    {
        //
        try {
            $validate = $request->validate([
                'npm' => 'required',
                'nama_mahasiswa' => 'required',
                'id_kelas' => 'required',
                'kode_prodi' => 'required'

            ]);

            Http::asForm()->put("http://localhost:8080/mahasiswa/$mahasiswa", $validate);

            return redirect()->route('Mahasiswa.index')->with('success', 'Mahasiswa berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($mahasiswa)
    {
        //
        Http::delete("http://localhost:8080/mahasiswa/$mahasiswa");
        return redirect()->route('Mahasiswa.index');
    }
}
```


2. MatakuliahController.php
```
<?php

namespace App\Http\Controllers;

use App\Models\Matkul;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class MatakuliahController extends Controller
{
    
    public function index()
    {
        //
        $response = Http::get('http://localhost:8080/matakuliah');

        if ($response->successful()) {
            $prodi = collect($response->json())->sortBy('kode_matkul')->values();
            return view('Matakuliah', compact('matakuliah'));
        } else {
            return back()->with('error', 'Gagal mengambil data prodi');
        }
    }

    
    public function create()
    {
        //
        return view('tambahmatkul');
    }

   
    public function store(Request $request)
    {
        //
        try {
            $validate = $request->validate([
                'kode_matkul' => 'required',
                'nama_matkul' => 'required',
                'semester' => 'required',
                'sks' => 'required',
            ]);

            Http::asForm()->post('http://localhost:8080/matakuliah', $validate);

            return redirect()->route('Matakuliah.index')->with('success', 'Matkul berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($matakuliah)
    {
        //
        $response = Http::get("http://localhost:8080/matakuliah/$matakuliah");

        if ($response->successful() && !empty($response[0])) {
            $matakuliah = $response[0]; // karena CodeIgniter mengembalikan array berisi 1 data
            return view('editmatakuliah', compact('matakuliah'));
        } else {
            return back()->with('error', 'Gagal mengambil data kelas');
        }

    }

    public function update(Request $request, $matakuliah)
    {
        //
        try {
            $validate = $request->validate([
                'kode_matkul' => 'required',
                'nama_matkul' => 'required',
                'semester' => 'required',
                'sks' => 'required',
            ]);

            Http::asForm()->put("http://localhost:8080/matakuliah/$matakuliah", $validate);

            return redirect()->route('Matakuliah.index')->with('success', 'Matkul berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }

    public function destroy($matakuliah)
    {
        
        Http::delete("http://localhost:8080/matakuliah/$matakuliah");
        return redirect()->route('Matakuliah.index');
    }

    public function exportPdf()
    {
        $response = Http::get('http://localhost:8080/matakuliah');
        if ($response->successful()) {
            $prodi = collect($response->json());
            $pdf = Pdf::loadView('pdf.cetak', compact('matakuliah')); // Ubah 'cetak.pdf' menjadi 'pdf.cetak'
            return $pdf->download('matkul.pdf');
        } else {
            return back()->with('error', 'Gagal mengambil data untuk PDF');
        }
    }
}

```

## 🧩 Models

1. Mahasiswa.php
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class matakuliah extends Model
{
    
    use HasFactory;
    protected $table = 'matakuliah';
}
```
2. Matakuliah.php
```
<?php

namespace App\Http\Controllers;

use App\Models\Matkul;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class MatakuliahController extends Controller
{
    
    public function index()
    {
        //
        $response = Http::get('http://localhost:8080/matakuliah');

        if ($response->successful()) {
            $prodi = collect($response->json())->sortBy('kode_matkul')->values();
            return view('Matakuliah', compact('matakuliah'));
        } else {
            return back()->with('error', 'Gagal mengambil data prodi');
        }
    }

    
    public function create()
    {
        //
        return view('tambahmatkul');
    }

   
    public function store(Request $request)
    {
        //
        try {
            $validate = $request->validate([
                'kode_matkul' => 'required',
                'nama_matkul' => 'required',
                'semester' => 'required',
                'sks' => 'required',
            ]);

            Http::asForm()->post('http://localhost:8080/matakuliah', $validate);

            return redirect()->route('Matakuliah.index')->with('success', 'Matkul berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($matakuliah)
    {
        //
        $response = Http::get("http://localhost:8080/matakuliah/$matakuliah");

        if ($response->successful() && !empty($response[0])) {
            $matakuliah = $response[0]; // karena CodeIgniter mengembalikan array berisi 1 data
            return view('editmatakuliah', compact('matakuliah'));
        } else {
            return back()->with('error', 'Gagal mengambil data kelas');
        }

    }

    public function update(Request $request, $matakuliah)
    {
        //
        try {
            $validate = $request->validate([
                'kode_matkul' => 'required',
                'nama_matkul' => 'required',
                'semester' => 'required',
                'sks' => 'required',
            ]);

            Http::asForm()->put("http://localhost:8080/matakuliah/$matakuliah", $validate);

            return redirect()->route('Matakuliah.index')->with('success', 'Matkul berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }

    public function destroy($matakuliah)
    {
        
        Http::delete("http://localhost:8080/matakuliah/$matakuliah");
        return redirect()->route('Matakuliah.index');
    }

    public function exportPdf()
    {
        $response = Http::get('http://localhost:8080/matakuliah');
        if ($response->successful()) {
            $prodi = collect($response->json());
            $pdf = Pdf::loadView('pdf.cetak', compact('matakuliah')); // Ubah 'cetak.pdf' menjadi 'pdf.cetak'
            return $pdf->download('matkul.pdf');
        } else {
            return back()->with('error', 'Gagal mengambil data untuk PDF');
        }
    }
}
```

