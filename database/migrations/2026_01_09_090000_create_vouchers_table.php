<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id('id_voucher');
            $table->string('kode_voucher')->unique();
            $table->string('nama_voucher');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah_diskon', 12, 2);
            $table->decimal('minimal_transaksi', 12, 2)->default(0);
            $table->enum('tipe_voucher', ['welcome', 'promo', 'special'])->default('promo');
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->boolean('is_active')->default(true);
            $table->integer('maksimal_penggunaan')->nullable(); // NULL = unlimited
            $table->integer('total_digunakan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
