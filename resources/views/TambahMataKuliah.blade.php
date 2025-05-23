<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Prodi</title>
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
            <button onclick="redirectToProdi()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
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
            window.location.href = "{{ route('Prodi.index') }}";
        }
    </script>

</body>

</html>
