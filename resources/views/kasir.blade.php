<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>#video{width:100%;display:block;transform:none!important}</style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 p-3 sm:p-6">
<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">🛒 Kasir</h1>
        <div class="flex gap-2">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('barang.index') }}" class="px-4 py-2 rounded-xl bg-white/10 text-sm">Kelola Barang</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="px-4 py-2 rounded-xl bg-white/10 text-sm">Logout</button>
            </form>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-4">

        <!-- KIRI: scan -->
        <div class="bg-white/5 border border-white/10 rounded-3xl p-5">
            <label class="text-sm text-slate-400">Scan barcode (scanner USB) atau ketik, lalu Enter</label>
            <div class="flex gap-2 mt-1">
                <input id="kode" autofocus autocomplete="off" inputmode="numeric" placeholder="Barcode..."
                       class="flex-1 px-4 py-3 rounded-xl bg-white/10 outline-none focus:ring-2 focus:ring-cyan-400">
                <button id="btnTambah" class="px-4 py-3 rounded-xl bg-pink-500 font-semibold">Tambah</button>
            </div>
            <button id="btnCam" class="mt-3 w-full px-4 py-3 rounded-xl bg-cyan-500 text-slate-900 font-semibold">📷 Scan dengan Kamera</button>

            <div id="camBox" class="hidden mt-3">
                <select id="camSelect" class="w-full mb-2 px-3 py-2 rounded-xl bg-slate-800 text-sm"></select>
                <div class="rounded-2xl overflow-hidden bg-black relative">
                    <video id="video" muted playsinline autoplay></video>
                    <div class="absolute left-[5%] right-[5%] top-1/2 h-0.5 bg-rose-500 shadow-[0_0_10px_#f43f5e]"></div>
                </div>
                <p class="text-xs text-slate-400 mt-2">Kamera tetap menyala. Setiap barcode yang terbaca langsung masuk keranjang.</p>
            </div>

            <p id="pesan" class="mt-3 text-sm min-h-[1.5rem]"></p>
        </div>

        <!-- KANAN: keranjang & bayar -->
        <div class="bg-white/5 border border-white/10 rounded-3xl p-5">
            <h2 class="font-semibold mb-3">Keranjang</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-slate-400 text-left">
                        <tr><th class="py-2">Barang</th><th>Harga</th><th class="w-20">Jml</th><th class="text-right">Subtotal</th><th></th></tr>
                    </thead>
                    <tbody id="isi"></tbody>
                </table>
            </div>
            <p id="kosong" class="text-center text-slate-500 py-6">Belum ada barang. Scan dulu.</p>

            <div class="border-t border-white/10 mt-3 pt-3 space-y-3">
                <div class="flex justify-between text-xl font-bold">
                    <span>TOTAL</span><span id="total">Rp 0</span>
                </div>

                <div>
                    <label class="text-sm text-slate-400">Uang dari customer</label>
                    <input id="bayar" type="number" min="0" inputmode="numeric"
                           class="w-full px-4 py-3 rounded-xl bg-white/10 outline-none text-lg focus:ring-2 focus:ring-emerald-400">
                    <div class="flex gap-2 mt-2 flex-wrap text-sm">
                        <button type="button" id="btnPas" class="px-3 py-1 rounded-lg bg-white/10">Uang pas</button>
                        <button type="button" data-uang="20000" class="uang px-3 py-1 rounded-lg bg-white/10">20.000</button>
                        <button type="button" data-uang="50000" class="uang px-3 py-1 rounded-lg bg-white/10">50.000</button>
                        <button type="button" data-uang="100000" class="uang px-3 py-1 rounded-lg bg-white/10">100.000</button>
                    </div>
                </div>

                <div class="flex justify-between text-lg">
                    <span>Kembalian</span><span id="kembali" class="font-bold">Rp 0</span>
                </div>

                <button id="btnBayar" disabled
                        class="w-full py-3 rounded-xl bg-emerald-500 text-slate-900 font-bold text-lg disabled:opacity-40">
                    Bayar &amp; Cetak Nota
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Popup sukses -->
<div id="sukses" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
    <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 w-full max-w-sm text-center">
        <div class="text-5xl">✅</div>
        <h3 class="text-xl font-bold mt-2">Transaksi Berhasil</h3>
        <p class="text-slate-400 text-sm" id="sKode"></p>
        <p class="mt-3">Total: <b id="sTotal"></b></p>
        <p class="text-2xl mt-1">Kembalian: <b id="sKembali" class="text-emerald-400"></b></p>
        <a id="sNota" target="_blank" class="block mt-4 py-3 rounded-xl bg-amber-400 text-slate-900 font-bold">📄 Buka Nota PDF</a>
        <button id="sBaru" class="mt-2 w-full py-3 rounded-xl bg-white/10">Transaksi Baru</button>
    </div>
</div>

<script type="module">
    import { BarcodeDetector } from 'https://cdn.jsdelivr.net/npm/barcode-detector@3/dist/es/ponyfill.min.js';

    const $ = id => document.getElementById(id);
    const rupiah = n => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    let cart = [];
    let detector = null, stream = null, scanning = false;
    let lastCode = '', lastTime = 0;

    function pesan(teks, warna = 'text-slate-300') {
        $('pesan').className = 'mt-3 text-sm min-h-[1.5rem] ' + warna;
        $('pesan').textContent = teks;
    }

    function beep(nada = 1100) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const o = ctx.createOscillator(), g = ctx.createGain();
            o.type = 'square'; o.frequency.value = nada; g.gain.value = 0.08;
            o.connect(g); g.connect(ctx.destination);
            o.start(); setTimeout(() => { o.stop(); ctx.close(); }, 120);
        } catch (e) {}
    }

    const total = () => cart.reduce((s, i) => s + i.harga * i.qty, 0);

    function render() {
        $('isi').innerHTML = cart.map(i => `
            <tr class="border-t border-white/10">
                <td class="py-2">${esc(i.nama)}<div class="text-xs text-slate-500">${esc(i.barcode)} · stok ${i.stok}</div></td>
                <td>${rupiah(i.harga)}</td>
                <td><input type="number" min="1" max="${i.stok}" value="${i.qty}"
                     onchange="setQty(${i.id}, this.value)"
                     class="w-16 px-2 py-1 rounded-lg bg-white/10 outline-none"></td>
                <td class="text-right">${rupiah(i.harga * i.qty)}</td>
                <td class="text-right"><button onclick="hapus(${i.id})" class="text-red-400 px-2">✕</button></td>
            </tr>`).join('');
        $('kosong').classList.toggle('hidden', cart.length > 0);
        $('total').textContent = rupiah(total());
        hitung();
    }

    function hitung() {
        const bayar = parseInt($('bayar').value || 0, 10);
        const sisa = bayar - total();
        const k = $('kembali');
        if (cart.length && sisa < 0) { k.textContent = 'Kurang ' + rupiah(-sisa); k.className = 'font-bold text-red-400'; }
        else { k.textContent = rupiah(Math.max(sisa, 0)); k.className = 'font-bold text-emerald-400'; }
        $('btnBayar').disabled = !(cart.length && bayar >= total());
    }

    window.setQty = (id, v) => {
        const i = cart.find(x => x.id === id); if (!i) return;
        let q = parseInt(v, 10);
        if (!q || q < 1) q = 1;
        if (q > i.stok) { q = i.stok; pesan(`Stok ${i.nama} hanya ${i.stok}`, 'text-amber-400'); }
        i.qty = q; render();
    };
    window.hapus = id => { cart = cart.filter(x => x.id !== id); render(); };

    async function tambahDariBarcode(kode) {
        kode = kode.trim();
        if (!kode) return;
        try {
            const r = await fetch(`{{ route('kasir.cari') }}?barcode=${encodeURIComponent(kode)}`, { headers: { Accept: 'application/json' } });
            if (!r.ok) { beep(300); pesan(`❌ Barcode ${kode} tidak ditemukan`, 'text-red-400'); return; }
            const p = await r.json();
            if (p.stok < 1) { beep(300); pesan(`⚠️ ${p.nama} stok habis`, 'text-amber-400'); return; }

            const ada = cart.find(x => x.id === p.id);
            if (ada) {
                if (ada.qty >= p.stok) { pesan(`⚠️ Stok ${p.nama} hanya ${p.stok}`, 'text-amber-400'); return; }
                ada.qty++; ada.stok = p.stok;
            } else {
                cart.push({ ...p, qty: 1 });
            }
            beep();
            pesan(`✅ ${p.nama} - ${rupiah(p.harga)}`, 'text-emerald-400');
            render();
        } catch (e) {
            pesan('❌ Gagal menghubungi server', 'text-red-400');
        }
    }

    // input manual / scanner USB
    $('kode').addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); tambahDariBarcode($('kode').value); $('kode').value = ''; }
    });
    $('btnTambah').addEventListener('click', () => { tambahDariBarcode($('kode').value); $('kode').value = ''; $('kode').focus(); });

    // pembayaran
    $('bayar').addEventListener('input', hitung);
    $('btnPas').addEventListener('click', () => { $('bayar').value = total(); hitung(); });
    document.querySelectorAll('.uang').forEach(b => b.addEventListener('click', () => { $('bayar').value = b.dataset.uang; hitung(); }));

    $('btnBayar').addEventListener('click', async () => {
        $('btnBayar').disabled = true;
        $('btnBayar').textContent = 'Memproses...';
        try {
            const r = await fetch(`{{ route('kasir.checkout') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({
                    items: cart.map(i => ({ id: i.id, qty: i.qty })),
                    bayar: parseInt($('bayar').value, 10),
                }),
            });
            const d = await r.json();
            if (!r.ok) {
                const err = d.errors ? Object.values(d.errors).flat().join(' ') : (d.message || 'Gagal');
                pesan('❌ ' + err, 'text-red-400');
                return;
            }
            stopCam();
            $('sKode').textContent = d.kode;
            $('sTotal').textContent = rupiah(d.total);
            $('sKembali').textContent = rupiah(d.kembalian);
            $('sNota').href = d.nota_url;
            $('sukses').classList.remove('hidden');
            window.open(d.nota_url, '_blank');
        } catch (e) {
            pesan('❌ Gagal memproses transaksi', 'text-red-400');
        } finally {
            $('btnBayar').textContent = 'Bayar & Cetak Nota';
            hitung();
        }
    });

    $('sBaru').addEventListener('click', () => {
        cart = []; $('bayar').value = ''; render();
        $('sukses').classList.add('hidden'); pesan('');
        $('kode').focus();
    });

    // kamera
    function stopCam() {
        scanning = false;
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        $('video').srcObject = null;
        $('camBox').classList.add('hidden');
        $('btnCam').textContent = '📷 Scan dengan Kamera';
    }

    async function loop() {
        while (scanning) {
            try {
                if ($('video').readyState >= 2) {
                    const hasil = await detector.detect($('video'));
                    if (hasil.length) {
                        const kode = hasil[0].rawValue, now = Date.now();
                        if (kode !== lastCode || now - lastTime > 2500) {
                            lastCode = kode; lastTime = now;
                            await tambahDariBarcode(kode);
                        }
                    }
                }
            } catch (e) {}
            await new Promise(r => setTimeout(r, 150));
        }
    }

    async function runCam(deviceId) {
        scanning = false;
        if (stream) stream.getTracks().forEach(t => t.stop());
        stream = await navigator.mediaDevices.getUserMedia({
            video: { deviceId: deviceId ? { exact: deviceId } : undefined, width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        $('video').srcObject = stream;
        await $('video').play();
        const track = stream.getVideoTracks()[0];
        const caps = track.getCapabilities ? track.getCapabilities() : {};
        if (caps.focusMode && caps.focusMode.includes('continuous')) {
            track.applyConstraints({ advanced: [{ focusMode: 'continuous' }] }).catch(() => {});
        }
        localStorage.setItem('lastCam', deviceId || '');
        scanning = true; loop();
    }

    $('btnCam').addEventListener('click', async () => {
        if (scanning) { stopCam(); return; }
        $('camBox').classList.remove('hidden');
        $('btnCam').textContent = '⏹ Matikan Kamera';
        pesan('Menyiapkan kamera...');
        try {
            if (!detector) detector = new BarcodeDetector({ formats: ['ean_13','ean_8','upc_a','upc_e','code_128','code_39'] });
            const tmp = await navigator.mediaDevices.getUserMedia({ video: true });
            tmp.getTracks().forEach(t => t.stop());
            const devs = (await navigator.mediaDevices.enumerateDevices()).filter(d => d.kind === 'videoinput');
            $('camSelect').innerHTML = devs.map((d, i) => `<option value="${d.deviceId}">${esc(d.label || 'Kamera ' + (i + 1))}</option>`).join('');
            const last = localStorage.getItem('lastCam');
            const back = devs.find(d => /back|rear|environment|belakang/i.test(d.label));
            const pick = devs.find(d => d.deviceId === last) || back || devs[devs.length - 1];
            $('camSelect').value = pick.deviceId;
            await runCam(pick.deviceId);
            pesan('Arahkan kamera ke barcode...');
        } catch (e) {
            pesan('❌ Kamera gagal: ' + (e.name || '') + ' ' + (e.message || ''), 'text-red-400');
            stopCam();
        }
    });
    $('camSelect').addEventListener('change', () => runCam($('camSelect').value).catch(() => {}));
    window.addEventListener('beforeunload', stopCam);

    render();
</script>
</body>
</html>