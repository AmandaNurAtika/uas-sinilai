<?php

namespace App\Http\Controllers;

use App\Controllers\MataKuliah;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\Kelas;
use App\Models\Dashboard;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $jumlahMahasiswa = Mahasiswa::count();
    $jumlahMataKuliah = MataKuliah::count();

    return view('Dashboard', compact('jumlahMahasiswa', 'jumlahProdi', 'jumlahKelas'));
}

}
