<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 p-6">
<div class="max-w-5xl mx-auto bg-white/5 border border-white/10 rounded-3xl p-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Kelola Barang</h1>
        <div class="flex gap-2">
            <a href="{{ route('barang.create') }}" class="px-4 py-2 rounded-xl bg-pink-500 font-semibold">+ Tambah</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="px-4 py-2 rounded-xl bg-white/10">Logout</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-xl bg-green-500/20 text-green-300">{{ session('success') }}</div>
    @endif

    <form class="mb-4 flex gap-2">
        <input name="q" value="{{ request('q') }}" autofocus placeholder="Cari nama / scan barcode..."
               class="flex-1 px-4 py-2 rounded-xl bg-white/10 outline-none">
        <button class="px-4 py-2 rounded-xl bg-white/10">Cari</button>
    </form>

    <div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead class="text-slate-400 text-sm uppercase">
            <tr><th class="p-3">Barcode</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($produks as $p)
            <tr class="border-t border-white/10">
                <td class="p-3 bg-white rounded">
                    <svg class="barcode" data-code="{{ $p->barcode }}"></svg>
                </td>
                <td>{{ $p->nama }}</td>
                <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                <td>{{ $p->stok }}</td>
                <td class="space-x-2">
                    <a href="{{ route('barang.edit', $p) }}" class="text-cyan-300">Edit</a>
                    <form method="POST" action="{{ route('barang.destroy', $p) }}" class="inline"
                          onsubmit="return confirm('Hapus barang ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-6 text-center text-slate-400">Belum ada barang</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>

    <div class="mt-4">{{ $produks->links() }}</div>
</div>

<script>
document.querySelectorAll('svg.barcode').forEach(el => {
    try {
        JsBarcode(el, el.dataset.code, { height: 40, width: 1.5, fontSize: 12, margin: 4 });
    } catch (e) {
        JsBarcode(el, el.dataset.code, { format: 'CODE128', height: 40, width: 1.5, fontSize: 12, margin: 4 });
    }
});
</script>
</body>
</html>