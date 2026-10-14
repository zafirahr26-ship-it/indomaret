<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilik - POS Minimarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen relative overflow-x-hidden font-sans flex items-center justify-center p-6">

    <!-- Colorful Glowing Background Blobs -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-pink-500/30 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute top-1/2 right-1/3 w-80 h-80 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Glassmorphism Main Card -->
    <div class="relative z-10 w-full max-w-4xl bg-white/10 backdrop-blur-xl border border-white/20 p-8 rounded-3xl shadow-2xl shadow-pink-500/10">
        
        <!-- Header Section -->
        <div class="flex justify-between items-center pb-6 border-b border-white/10 mb-6">
            <div>
                <h1 class="text-3xl font-extrabold bg-gradient-to-r from-pink-400 via-purple-300 to-cyan-300 bg-clip-text text-transparent">
                    Dashboard Pemilik Toko
                </h1>
                <p class="text-slate-300 text-sm mt-1">
                    Selamat datang kembali, <span class="text-pink-300 font-semibold">{{ Auth::user()->name }}</span>!
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="/barang" class="bg-white/10 hover:bg-white/20 text-white text-sm font-medium px-4 py-2.5 rounded-xl border border-white/20 transition backdrop-blur-md">
                    Kelola Barang
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-gradient-to-r from-pink-500 to-rose-600 hover:from-pink-600 hover:to-rose-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition shadow-lg shadow-pink-500/30">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Metric Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-6">
            <!-- Card 1 -->
            <div class="bg-white/5 border border-white/10 p-5 rounded-2xl backdrop-blur-md hover:border-pink-500/40 transition">
                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Total Penjualan</p>
                <h3 class="text-2xl font-bold text-pink-400 mt-2">Rp 1.450.000</h3>
                <span class="text-xs text-emerald-400 font-medium">↑ +12% hari ini</span>
            </div>

            <!-- Card 2 -->
            <div class="bg-white/5 border border-white/10 p-5 rounded-2xl backdrop-blur-md hover:border-purple-500/40 transition">
                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Transaksi Hari Ini</p>
                <h3 class="text-2xl font-bold text-purple-300 mt-2">38 Transaksi</h3>
                <span class="text-xs text-purple-400 font-medium">Kasir Aktif</span>
            </div>

            <!-- Card 3 -->
            <div class="bg-white/5 border border-white/10 p-5 rounded-2xl backdrop-blur-md hover:border-cyan-500/40 transition">
                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Total Produk</p>
                <h3 class="text-2xl font-bold text-cyan-300 mt-2">120 Item</h3>
                <span class="text-xs text-cyan-400 font-medium">Stok Aman</span>
            </div>
        </div>

    </div>

</body>
</html>