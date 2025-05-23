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