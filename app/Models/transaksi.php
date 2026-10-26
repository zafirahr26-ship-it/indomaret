<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = ['kode', 'user_id', 'total', 'bayar', 'kembalian'];

    public function items()
    {
        return $this->hasMany(TransaksiItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}