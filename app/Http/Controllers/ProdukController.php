<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $produks = Produk::query()
            ->when($request->q, fn ($q, $s) =>
                $q->where('nama', 'like', "%$s%")->orWhere('barcode', 'like', "%$s%"))
            ->latest()->paginate(10)->withQueryString();

        return view('barang.index', compact('produks'));
    }

    public function create()
    {
        return view('barang.form', ['produk' => new Produk()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        // Kalau barcode dikosongkan, generate otomatis (EAN-13 style 13 digit)
        $data['barcode'] = $data['barcode'] ?: $this->generateBarcode();
        Produk::create($data);

        return redirect()->route('barang.index')->with('success', 'Barang ditambahkan');
    }

    public function edit(Produk $produk)
    {
        return view('barang.form', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $produk->update($this->validated($request, $produk->id));
        return redirect()->route('barang.index')->with('success', 'Barang diperbarui');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return back()->with('success', 'Barang dihapus');
    }

    private function validated(Request $request, $id = null): array
    {
        return $request->validate([
            'barcode' => 'nullable|string|max:50|unique:produks,barcode,' . $id,
            'nama'    => 'required|string|max:150',
            'harga'   => 'required|integer|min:0',
            'stok'    => 'required|integer|min:0',
        ]);
    }

    private function generateBarcode(): string
    {
        do {
            $code = '899' . str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
            // checksum EAN-13
            $sum = 0;
            foreach (str_split($code) as $i => $d) {
                $sum += $d * ($i % 2 === 0 ? 1 : 3);
            }
            $code .= (10 - ($sum % 10)) % 10;
        } while (Produk::where('barcode', $code)->exists());

        return $code;
    }
}