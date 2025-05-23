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