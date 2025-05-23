<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Data Program Studi</title>
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
          <th class="py-1 px-2">Kode Prodi</th>
          <th class="py-1 px-2">Nama Prodi</th>
          <th class="py-1 px-2 text-center w-24">Aksi</th>
        </tr>
      </thead>
      <tbody id="prodiTable">
        <?php $no = 1; ?>
        @foreach($prodi as $p)
        <tr class="border-b border-gray-200 hover:bg-gray-50">
          <td class="py-1 px-2 text-center">{{ $no++ }}</td>
          <td class="py-1 px-2">{{ $p['kode_prodi'] }}</td>
          <td class="py-1 px-2">{{ $p['nama_prodi'] }}</td>
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
