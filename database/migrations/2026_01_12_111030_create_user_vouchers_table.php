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
        Schema::create('user_vouchers', function (Blueprint $table) {
            $table->id('id_user_voucher');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_voucher')->constrained('vouchers', 'id_voucher')->onDelete('cascade');
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->foreignId('id_pemesanan')->nullable()->constrained('pemesanan', 'id_pemesanan')->onDelete('set null');
            $table->timestamps();

            // Unique constraint: user bisa punya voucher yang sama, tapi hanya 1 yang belum dipakai
            $table->unique(['id_user', 'id_voucher', 'is_used'], 'unique_user_voucher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vouchers');
    }
};
