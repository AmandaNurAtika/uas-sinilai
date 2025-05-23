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
## 🧩 Models

1. Dashboard.blade.php
```
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Nilai - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 font-sans">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-56 bg-white border-r border-gray-200 px-4 py-6">
            <h1 class="text-2xl font-bold text-blue-600 mb-8 text-center">Sinilai Online</h1>
            <nav class="space-y-2">
                <a href="{{ route('Dashboard.index') }}" class="block text-gray-700 hover:text-blue-600">Dashboard</a>
                <div>
                    <button id="toggleMenu" class="w-full text-left text-gray-700 hover:text-blue-600">Data Master</button>
                    <div id="submenu" class="pl-4 mt-1 space-y-1 hidden">
                        <a href="{{ route('Mahasiswa.index') }}" class="block text-sm text-gray-600 hover:text-blue-600">Mahasiswa</a>
                        <a href="{{ route('Matakuliah.index') }}" class="block text-sm text-gray-600 hover:text-blue-600">Mata Kuliah</a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">

            <!-- Topbar -->
            <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Dashboard</h2>
            </header>

            <!-- Dashboard Cards -->
            <main class="flex-1 p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded shadow p-4">
                        <p class="text-sm text-gray-500">Tahun Ajaran</p>
                        <p class="text-xl font-bold text-blue-600">2024/2025</p>
                    </div>
                    <div class="bg-white rounded shadow p-4">
                        <p class="text-sm text-gray-500">Jumlah Mahasiswa</p>
                        <p class="text-xl font-bold text-green-600">{{ $jumlahMahasiswa }}</p>
                    </div>
                    <div class="bg-white rounded shadow p-4">
                        <p class="text-sm text-gray-500">Jumlah Mata Kuliah</p>
                        <p class="text-xl font-bold text-purple-600">{{ $jumlahMataKuliah }}</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow-lg w-80 text-center">
            <h3 class="font-semibold mb-4">Konfirmasi Logout</h3>
            <p>Anda yakin ingin keluar?</p>
            <div class="mt-4 flex justify-center gap-4">
                <button onclick="confirmLogout()" class="bg-red-600 text-white px-4 py-2 rounded">Logout</button>
                <button onclick="closeLogoutModal()" class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Batal</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.getElementById("toggleMenu").addEventListener("click", function() {
            document.getElementById("submenu").classList.toggle("hidden");
        });

        document.getElementById("userMenu").addEventListener("click", function() {
            document.getElementById("userDropdown").classList.toggle("hidden");
        });

        function openLogoutModal(e) {
            e.preventDefault();
            document.getElementById("logoutModal").classList.remove("hidden");
        }

        function closeLogoutModal() {
            document.getElementById("logoutModal").classList.add("hidden");
        }

        function confirmLogout() {
            window.location.href = "/logout";
        }
    </script>

</body>

</html>
```
2.  Mahasiswa.blade.php
```
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Data Mahasiswa</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

  <div class="max-w-4xl mx-auto p-4 mt-10 bg-white rounded shadow">

    <header class="mb-4 flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-700">Daftar Mahasiswa</h1>
      <a href="{{ route('Mahasiswa.create') }}" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
        Tambah
      </a>
    </header>

    <input id="searchInput" type="text" placeholder="Cari nama mahasiswa..." 
      class="w-full p-2 mb-4 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" />

    <table class="w-full text-left text-gray-700 text-sm">
      <thead>
        <tr class="border-b border-gray-300">
          <th class="py-1 px-2 text-center w-10">No</th>
          <th class="py-1 px-2">NPM</th>
          <th class="py-1 px-2">Nama</th>
          <th class="py-1 px-2">Matkul</th>
          <th class="py-1 px-2 text-center w-24">Aksi</th>
        </tr>
      </thead>
      <tbody id="mahasiswaTable">
        <?php $no = 1; ?>
        @foreach($mahasiswa as $mhs)
        <tr class="border-b border-gray-200 hover:bg-gray-50">
          <td class="py-1 px-2 text-center">{{ $no++ }}</td>
          <td class="py-1 px-2">{{ $mhs['npm'] }}</td>
          <td class="py-1 px-2">{{ $mhs['nama_mahasiswa'] }}</td>
          <td class="py-1 px-2">{{ $mhs['nama_matkul'] }}</td>
          <td class="py-1 px-2 text-center space-x-1">
            <a href="{{ route('Mahasiswa.edit', $mhs['npm']) }}" class="text-green-600 hover:text-green-800">Edit</a>
            <form class="inline" method="POST" action="{{ route('Mahasiswa.destroy', $mhs['npm']) }}" 
              onsubmit="return confirm('Yakin ingin menghapus?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

  </div>

  <script>
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function () {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('#mahasiswaTable tr');
      rows.forEach(row => {
        const name = row.cells[2].textContent.toLowerCase();
        row.style.display = name.includes(filter) ? '' : 'none';
      });
    });
  </script>

</body>

</html>


```
3. Tambahmahasiswa.blade.php
```
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Tambah Data Mahasiswa</h2>
        <form action="{{ route('Mahasiswa.store') }}" method="post">
            @csrf

            <div class="mb-4">
                <label for="npm" class="block font-medium mb-1">NPM</label>
                <input type="text" id="npm" name="npm" class="w-full p-2 border rounded" required>
            </div>

            <div class="mb-4">
                <label for="nama_mahasiswa" class="block font-medium mb-1">Nama Mahasiswa</label>
                <input type="text" id="nama_mahasiswa" name="nama_mahasiswa" class="w-full p-2 border rounded" required>
            </div>

            <div class="mb-4">
                <label for="id_kelas" class="block font-medium mb-1">KODE MATKUL</label>
                <select id="id_kelas" name="id_kelas" class="w-full p-2 border rounded" required>
                    @foreach($kelas as $row)
                    <option value="{{ $row['id_kelas'] }}">{{ $row['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="kode_prodi" class="block font-medium mb-1">ID Prodi</label>
                <select id="kode_prodi" name="kode_prodi" class="w-full p-2 border rounded" required>
                    @foreach($prodi as $row)
                    <option value="{{ $row['kode_prodi'] }}">{{ $row['id_prodi'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('Mahasiswa.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                    Batal
                </a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Submit
                </button>
            </div>
        </form>
    </div>
</body>

</html>

```
4. Editmahasiswa.blade.php
```
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Edit Data Mahasiswa</h2>
        <form action="{{ route('Mahasiswa.update', $mahasiswa['npm']) }}" method="post">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="npm" class="block font-medium mb-1">NPM</label>
                <input type="text" id="npm" name="npm" value="{{ $mahasiswa['npm'] }}" class="w-full p-2 border rounded" required />
            </div>

            <div class="mb-4">
                <label for="nama_mahasiswa" class="block font-medium mb-1">Nama Mahasiswa</label>
                <input type="text" id="nama_mahasiswa" name="nama_mahasiswa" value="{{ $mahasiswa['nama_mahasiswa'] }}" class="w-full p-2 border rounded" required />
            </div>

            <div class="mb-4">
                <label for="id_kelas" class="block font-medium mb-1">Kode Matkul</label>
                <select id="id_kelas" name="kode_matkul" class="w-full p-2 border rounded" required>
                    @foreach($kelas as $row)
                    <option value="{{ $row['kode_matkul'] }}" {{ $mahasiswa['kode_matkul'] == $row['id_kelas'] ? 'selected' : '' }}>
                        {{ $row['nama_kelas'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="kode_prodi" class="block font-medium mb-1">ID Prodi</label>
                <select id="kode_prodi" name="kode_prodi" class="w-full p-2 border rounded" required>
                    @foreach($prodi as $row)
                    <option value="{{ $row['kode_prodi'] }}" {{ $mahasiswa['kode_prodi'] == $row['kode_prodi'] ? 'selected' : '' }}>
                        {{ $row['nama_prodi'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('Mahasiswa.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                    Batal
                </a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Ubah Data
                </button>
            </div>
        </form>
    </div>
</body>

</html>

```
5. MataKuliah.blade.php
```
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Data Matkul</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

  <div class="max-w-4xl mx-auto p-4 mt-10 bg-white rounded shadow">

    <header class="mb-4 flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-700">Daftar Mata Kuliah</h1>
      <a href="{{ route('Prodi.create') }}" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
        Tambah
      </a>
    </header>

    <input id="searchInput" type="text" placeholder="Cari nama program studi..." 
      class="w-full p-2 mb-4 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" />

    <table class="w-full text-left text-gray-700 text-sm">
      <thead>
        <tr class="border-b border-gray-300">
          <th class="py-1 px-2 text-center w-10">No</th>
          <th class="py-1 px-2">Kode Matkul</th>
          <th class="py-1 px-2">Nama Matkul</th>
          <th class="py-1 px-2 text-center w-24">Aksi</th>
        </tr>
      </thead>
      <tbody id="prodiTable">
        <?php $no = 1; ?>
        @foreach($prodi as $p)
        <tr class="border-b border-gray-200 hover:bg-gray-50">
          <td class="py-1 px-2 text-center">{{ $no++ }}</td>
          <td class="py-1 px-2">{{ $p['kode_matkul'] }}</td>
          <td class="py-1 px-2">{{ $p['nama_matkul'] }}</td>
          <td class="py-1 px-2 text-center space-x-1">
            <a href="{{ route('Prodi.edit', $p['kode_prodi']) }}" class="text-green-600 hover:text-green-800">Edit</a>
            <form class="inline" method="POST" action="{{ route('Prodi.destroy', $p['kode_prodi']) }}" onsubmit="return confirm('Yakin ingin menghapus?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Tombol Cetak PDF -->
    <form action="{{ route('export.pdf') }}" method="GET" class="mt-4">
      <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition w-full">
        🖨 Cetak PDF
      </button>
    </form>

  </div>

  <script>
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function () {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('#prodiTable tr');
      rows.forEach(row => {
        const name = row.cells[2].textContent.toLowerCase();
        row.style.display = name.includes(filter) ? '' : 'none';
      });
    });
  </script>

</body>

</html>

```
6. TambahMatakuliah.blade.php
```
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Matkul</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-100 min-h-screen flex items-center justify-center">

    <!-- Modal Tambah Prodi -->
    <div id="formModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Tambah Data Mata Kuliah</h2>
            
            <form action="{{ route('Prodi.store') }}" method="post" onsubmit="showSuccessModal(); event.preventDefault();">
                @csrf

                <div class="mb-4">
                    <label for="kode_prodi" class="block font-medium mb-1">Kode Matkul</label>
                    <input type="text" id="kode_prodi" name="kode_prodi" class="w-full p-2 border rounded" required>
                </div>

                <div class="mb-6">
                    <label for="nama_prodi" class="block font-medium mb-1">Nama Matkul</label>
                    <input type="text" id="nama_prodi" name="nama_prodi" class="w-full p-2 border rounded" required>
                </div>

                 <div class="mb-6">
                    <label for="nama_prodi" class="block font-medium mb-1">Semester</label>
                    <input type="text" id="nama_prodi" name="nama_prodi" class="w-full p-2 border rounded" required>
                </div>

                 <div class="mb-6">
                    <label for="nama_prodi" class="block font-medium mb-1">SKS</label>
                    <input type="text" id="nama_prodi" name="nama_prodi" class="w-full p-2 border rounded" required>
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('Prodi.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Sukses -->
    <div id="successModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
            <div class="flex justify-center items-center mb-4">
                <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-blue-500 border-solid"></div>
            </div>
            <h2 class="text-lg font-bold mb-4">Data berhasil ditambahkan!</h2>
            <button onclick="redirectToMatakuliah()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                OK
            </button>
        </div>
    </div>

    <script>
        function showSuccessModal() {
            document.getElementById("formModal").classList.add("hidden");
            document.getElementById("successModal").classList.remove("hidden");
            setTimeout(() => {
                document.querySelector(".animate-spin").classList.add("hidden");
            }, 1000);
        }

        function redirectToProdi() {
            window.location.href = "{{ route('Matakuliah.index') }}";
        }
    </script>

</body>

</html>

```


# ✅ Membuat repository github
1. Buka https://github.com

2. Klik tombol “+” di pojok kanan atas → pilih "New repository"

3. Isi:
- Repository name: (misal: projectku)
- Visibility: pilih Public atau Private
- Klik Create repository

# ✅ Up Github
1. Masuk ke folder proyek kamu di komputer:
```
cd path/ke/folder/proyek
```
2. Inisialisasi Git 
```
git init
```
3. Tambahkan File dan Commit
```
git add .
git commit -m "Initial commit"
```
4. Tambahkan URL Repository GitHub
```
https://github.com/username/projectku.git
```

5. Lakukan git remote
```
git remote add origin https://github.com/username/projectku.git
```
6. Push ke GitHub
```
git branch -M main
git push -u origin main
```
