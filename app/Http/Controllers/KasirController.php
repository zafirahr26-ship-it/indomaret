<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir');
    }

    public function cari(Request $request)
    {
        $p = Produk::where('barcode', trim((string) $request->barcode))->first();

        if (!$p) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $p->id, 'barcode' => $p->barcode, 'nama' => $p->nama,
            'harga' => $p->harga, 'stok' => $p->stok,
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.id'  => 'required|integer|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'bayar'       => 'required|integer|min:0',
        ]);

        $items = collect($data['items'])->groupBy('id')->map(fn ($g) => $g->sum('qty'));

        $transaksi = DB::transaction(function () use ($items, $data) {
            $total = 0;
            $baris = [];

            foreach ($items as $id => $qty) {
                $p = Produk::lockForUpdate()->find($id);
                if ($p->stok < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$p->nama} tidak cukup (sisa {$p->stok}).",
                    ]);
                }
                $sub = $p->harga * $qty;
                $total += $sub;
                $baris[] = [
                    'produk_id' => $p->id, 'nama' => $p->nama, 'harga' => $p->harga,
                    'qty' => $qty, 'subtotal' => $sub,
                ];
                $p->decrement('stok', $qty);
            }

            if ($data['bayar'] < $total) {
                throw ValidationException::withMessages(['bayar' => 'Uang yang diberikan kurang.']);
            }

            $trx = Transaksi::create([
                'kode'      => 'TRX' . now()->format('ymdHis') . random_int(10, 99),
                'user_id'   => auth()->id(),
                'total'     => $total,
                'bayar'     => $data['bayar'],
                'kembalian' => $data['bayar'] - $total,
            ]);
            $trx->items()->createMany($baris);

            return $trx;
        });

        return response()->json([
            'id'        => $transaksi->id,
            'kode'      => $transaksi->kode,
            'total'     => $transaksi->total,
            'kembalian' => $transaksi->kembalian,
            'nota_url'  => route('kasir.nota', $transaksi),
        ]);
    }

    public function nota(Transaksi $transaksi)
    {
        $transaksi->load('items', 'user');

        return Pdf::loadView('nota', compact('transaksi'))
            ->setPaper('a6')
            ->stream('nota-' . $transaksi->kode . '.pdf');
    }
}