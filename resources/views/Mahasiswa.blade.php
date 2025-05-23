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
          <th class="py-1 px-2">Kelas</th>
          <th class="py-1 px-2">Prodi</th>
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
          <td class="py-1 px-2">{{ $mhs['nama_kelas'] }}</td>
          <td class="py-1 px-2">{{ $mhs['nama_prodi'] }}</td>
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
