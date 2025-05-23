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