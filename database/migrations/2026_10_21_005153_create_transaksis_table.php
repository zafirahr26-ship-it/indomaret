<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('total');
            $table->unsignedBigInteger('bayar');
            $table->unsignedBigInteger('kembalian');
            $table->timestamps();
        });

        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->string('nama');
            $table->unsignedBigInteger('harga');
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_items');
        Schema::dropIfExists('transaksis');
    }
};