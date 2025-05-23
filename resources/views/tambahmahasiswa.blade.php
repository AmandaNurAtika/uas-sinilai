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
                <label for="id_kelas" class="block font-medium mb-1">KODE KELAS</label>
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
