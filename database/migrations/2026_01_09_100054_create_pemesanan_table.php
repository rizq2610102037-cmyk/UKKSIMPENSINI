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
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id('id_pemesanan');
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->onDelete('cascade');

            $table->string('nama_pemesan');
            $table->string('email');
            $table->date('tgl_check_in');
            $table->date('tgl_check_out');
            $table->integer('jumlah_kamar');
            $table->enum('status_pemesanan', ['pending', 'confirm', 'checkin', 'checkout', 'cancel'])->default('pending');

            // Midtrans
            $table->string('order_id')->unique()->nullable();
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->string('snap_token')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])->default('pending');

            // addon
            $table->boolean('addon_sarapan')->default(false);
            $table->boolean('addon_alat_tidur')->default(false);

            // Kolom Denda
            $table->decimal('denda', 10, 2)->default(0);
            $table->text('keterangan_denda')->nullable();

            // Kolom Tracking checkout user
            $table->boolean('checkout_by_user')->default(false);
            $table->timestamp('user_checkout_at')->nullable();

            // Kolom tracking pembatalan booking oleh user
            $table->boolean('cancelled_by_user')->default(false);
            $table->timestamp('user_cancel_at')->nullable();
            $table->string('cancel_reason')->nullable();

            // Voucher Table Relation
            $table->foreignId('id_voucher')->nullable()->constrained('vouchers', 'id_voucher')->onDelete('set null');
            $table->decimal('diskon_voucher', 12, 2)->default(0);
            $table->string('kode_voucher_used')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanan');
    }
};
