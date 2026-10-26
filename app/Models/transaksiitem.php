<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $fillable = ['transaksi_id', 'produk_id', 'nama', 'harga', 'qty', 'subtotal'];
}