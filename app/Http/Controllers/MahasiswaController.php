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
