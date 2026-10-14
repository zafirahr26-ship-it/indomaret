<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Kasir - Minimarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen relative overflow-x-hidden font-sans p-6">

    <!-- Colorful Glowing Background Blobs -->
    <div class="absolute top-10 left-10 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 right-10 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-6xl mx-auto space-y-6">
        
        <!-- Top Navbar -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-5 rounded-2xl flex justify-between items-center shadow-xl">
            <div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-pink-400 to-purple-300 bg-clip-text text-transparent">
                    Halaman Transaksi Kasir
                </h1>
                <p class="text-xs text-slate-300 mt-0.5">Kasir Aktif: <span class="text-pink-300 font-semibold">{{ Auth::user()->name }}</span></p>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-gradient-to-r from-pink-500 to-rose-600 hover:from-pink-600 hover:to-rose-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-lg shadow-pink-500/30">
                    Logout
                </button>
            </form>
        </div>

        <!-- Section Scan Barcode (INPUT OTOMATIS SCANNER) -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-2xl shadow-xl">
            <label for="barcode" class="block text-sm font-semibold text-pink-300 mb-2">
                🔍 Scan Barcode Produk (Arahkan Scanner / Alat Scan ke Sini)
            </label>
            
            <form action="#" method="POST" class="flex gap-3">
                @csrf
                <!-- Atribut 'autofocus' membuat scanner langsung siap bekerja tanpa diklik manual -->
                <input type="text" 
                       name="barcode" 
                       id="barcode" 
                       autofocus 
                       placeholder="Scan barcode barang di sini..." 
                       class="w-full bg-slate-900/80 border border-pink-500/30 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-500/40 text-lg font-mono transition">
                
                <button type="submit" class="bg-pink-600 hover:bg-pink-500 text-white font-semibold px-6 py-3 rounded-xl transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Tabel Keranjang Belanja -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-2xl shadow-xl">
            <h2 class="text-lg font-bold text-slate-200 mb-4">Daftar Belanjaan</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 text-pink-300 text-xs font-bold uppercase">
                            <th class="p-3">Nama Produk</th>
                            <th class="p-3">Harga</th>
                            <th class="p-3">Jumlah</th>
                            <th class="p-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm text-slate-200">
                        <tr>
                            <td class="p-3 font-medium">Indomie Goreng Spesial</td>
                            <td class="p-3">Rp 3.100</td>
                            <td class="p-3">2</td>
                            <td class="p-3 font-semibold text-pink-400">Rp 6.200</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>