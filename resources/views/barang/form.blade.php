<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $produk->exists ? 'Edit' : 'Tambah' }} Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .cam-wrap { position: relative; border-radius: 16px; overflow: hidden; background: #000; }
        #video { width: 100%; display: block; transform: none !important; }
        .laser { position: absolute; left: 5%; right: 5%; top: 50%; height: 2px;
                 background: #f43f5e; box-shadow: 0 0 10px #f43f5e; pointer-events: none; }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 p-4 sm:p-6">
<div class="max-w-xl mx-auto bg-white/5 border border-white/10 rounded-3xl p-6 sm:p-8">
    <h1 class="text-2xl font-bold mb-6">{{ $produk->exists ? 'Edit' : 'Tambah' }} Barang</h1>

    @if($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-500/20 text-red-300">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ $produk->exists ? route('barang.update', $produk) : route('barang.store') }}"
          class="space-y-4">
        @csrf
        @if($produk->exists) @method('PUT') @endif

        <div>
            <label class="text-sm text-slate-400">Barcode (scan kamera, scanner USB, ketik, atau kosongkan untuk otomatis)</label>
            <div class="flex gap-2 mt-1">
                <input id="barcode" name="barcode" value="{{ old('barcode', $produk->barcode) }}"
                       inputmode="numeric" autocomplete="off"
                       class="flex-1 px-4 py-2 rounded-xl bg-white/10 outline-none focus:ring-2 focus:ring-cyan-400"
                       onkeydown="if(event.key==='Enter'){event.preventDefault();}">
                <button type="button" id="btnScan" class="px-4 py-2 rounded-xl bg-cyan-500 text-slate-900 font-semibold">📷 Scan</button>
            </div>

            <div id="scanBox" class="hidden mt-3">
                <select id="camSelect" class="w-full mb-2 px-3 py-2 rounded-xl bg-slate-800 text-slate-100 text-sm"></select>
                <div class="cam-wrap">
                    <video id="video" muted playsinline autoplay></video>
                    <div class="laser"></div>
                </div>
                <div id="zoomRow" class="hidden mt-3">
                    <label class="text-xs text-slate-400">Zoom</label>
                    <input type="range" id="zoom" class="w-full">
                </div>
                <p id="scanMsg" class="text-sm text-slate-300 mt-2">Menyiapkan scanner...</p>
                <button type="button" id="btnStop" class="mt-2 px-4 py-2 rounded-xl bg-white/10">Tutup kamera</button>
            </div>
        </div>

        <div>
            <label class="text-sm text-slate-400">Nama</label>
            <input name="nama" value="{{ old('nama', $produk->nama) }}" class="w-full px-4 py-2 rounded-xl bg-white/10 outline-none focus:ring-2 focus:ring-pink-400">
        </div>
        <div>
            <label class="text-sm text-slate-400">Harga</label>
            <input type="number" name="harga" value="{{ old('harga', $produk->harga) }}" class="w-full px-4 py-2 rounded-xl bg-white/10 outline-none focus:ring-2 focus:ring-pink-400">
        </div>
        <div>
            <label class="text-sm text-slate-400">Stok</label>
            <input type="number" name="stok" value="{{ old('stok', $produk->stok) }}" class="w-full px-4 py-2 rounded-xl bg-white/10 outline-none focus:ring-2 focus:ring-pink-400">
        </div>

        <div class="flex gap-2">
            <button class="px-5 py-2 rounded-xl bg-pink-500 font-semibold">Simpan</button>
            <a href="{{ route('barang.index') }}" class="px-5 py-2 rounded-xl bg-white/10">Batal</a>
        </div>
    </form>
</div>

<script type="module">
    import { BarcodeDetector } from 'https://cdn.jsdelivr.net/npm/barcode-detector@3/dist/es/ponyfill.min.js';

    const box = document.getElementById('scanBox');
    const msg = document.getElementById('scanMsg');
    const video = document.getElementById('video');
    const camSelect = document.getElementById('camSelect');
    const input = document.getElementById('barcode');
    const zoomRow = document.getElementById('zoomRow');
    const zoom = document.getElementById('zoom');

    let detector = null, stream = null, running = false;

    function beep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const o = ctx.createOscillator(), g = ctx.createGain();
            o.type = 'square'; o.frequency.value = 1100; g.gain.value = 0.08;
            o.connect(g); g.connect(ctx.destination);
            o.start(); setTimeout(() => { o.stop(); ctx.close(); }, 120);
        } catch (e) {}
    }

    function stopScan() {
        running = false;
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        video.srcObject = null;
        box.classList.add('hidden');
    }

    function berhasil(kode) {
        running = false;
        input.value = kode;
        beep();
        if (navigator.vibrate) navigator.vibrate(120);
        msg.textContent = '✅ Terbaca: ' + kode;
        setTimeout(() => { stopScan(); document.querySelector('[name=nama]').focus(); }, 500);
    }

    async function loop() {
        while (running) {
            try {
                if (video.readyState >= 2) {
                    const hasil = await detector.detect(video);
                    if (hasil.length) { berhasil(hasil[0].rawValue); return; }
                }
            } catch (e) { /* abaikan, coba frame berikutnya */ }
            await new Promise(r => setTimeout(r, 120));
        }
    }

    async function runCamera(deviceId) {
        running = false;
        if (stream) stream.getTracks().forEach(t => t.stop());

        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                deviceId: deviceId ? { exact: deviceId } : undefined,
                width: { ideal: 1920 }, height: { ideal: 1080 },
            }
        });
        video.srcObject = stream;
        await video.play();

        const track = stream.getVideoTracks()[0];
        const caps = track.getCapabilities ? track.getCapabilities() : {};
        if (caps.focusMode && caps.focusMode.includes('continuous')) {
            track.applyConstraints({ advanced: [{ focusMode: 'continuous' }] }).catch(() => {});
        }
        if (caps.zoom) {
            zoom.min = caps.zoom.min; zoom.max = caps.zoom.max; zoom.step = caps.zoom.step || 0.1;
            zoom.value = caps.zoom.min;
            zoomRow.classList.remove('hidden');
        } else {
            zoomRow.classList.add('hidden');
        }

        msg.textContent = 'Memindai... pegang barcode 15-30 cm dari kamera, garis barcode horizontal.';
        localStorage.setItem('lastCam', deviceId || '');
        running = true;
        loop();
    }

    async function startScan() {
        box.classList.remove('hidden');
        msg.textContent = 'Menyiapkan scanner (butuh internet sebentar)...';
        try {
            if (!detector) {
                detector = new BarcodeDetector({
                    formats: ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128', 'code_39'],
                });
            }
            const tmp = await navigator.mediaDevices.getUserMedia({ video: true });
            tmp.getTracks().forEach(t => t.stop());

            const devs = (await navigator.mediaDevices.enumerateDevices()).filter(d => d.kind === 'videoinput');
            if (!devs.length) { msg.textContent = '❌ Tidak ada kamera terdeteksi.'; return; }

            camSelect.innerHTML = devs.map((d, i) =>
                `<option value="${d.deviceId}">${d.label || 'Kamera ' + (i + 1)}</option>`).join('');
            const last = localStorage.getItem('lastCam');
            const pick = devs.find(d => d.deviceId === last) || devs[0];
            camSelect.value = pick.deviceId;

            await runCamera(pick.deviceId);
        } catch (err) {
            msg.textContent = '❌ Gagal: ' + (err.name || '') + ' - ' + (err.message || err);
        }
    }

    zoom.addEventListener('input', () => {
        const t = stream && stream.getVideoTracks()[0];
        if (t) t.applyConstraints({ advanced: [{ zoom: parseFloat(zoom.value) }] }).catch(() => {});
    });
    camSelect.addEventListener('change', () => runCamera(camSelect.value).catch(e => msg.textContent = '❌ ' + e));
    document.getElementById('btnScan').addEventListener('click', startScan);
    document.getElementById('btnStop').addEventListener('click', stopScan);
    window.addEventListener('beforeunload', stopScan);
</script>
</body>
</html>