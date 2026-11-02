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
        Schema::create('detail_pemesanan', function (Blueprint $table) {
            $table->id('id_detail_pemesanan');
            $table->foreignId('id_pemesanan')->constrained('pemesanan', 'id_pemesanan')->onDelete('cascade');
            $table->foreignId('id_tipe_kamar')->constrained('tipe_kamar', 'id_tipe_kamar')->onDelete('cascade');
            $table->foreignId('id_kamar')->nullable()->constrained('kamar', 'id_kamar')->onDelete('set null');
            $table->integer('jumlah_kamar')->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('harga_per_malam', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pemesanan');
    }
};
