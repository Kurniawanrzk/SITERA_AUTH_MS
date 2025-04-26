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
        Schema::create('bank_sampah_unit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');            
            $table->string('nomor_registrasi')->unique();
            $table->string('nama_bsu');
            $table->enum('kategori', ['bsu', 'bsi']);
            $table->text('alamat');
            $table->string('jalan_dusun');
            $table->string('rt');
            $table->string('rw');
            $table->string('desa');
            $table->string('kecamatan');
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->date('tanggal_berdiri');
            $table->string('nama_pengurus');
            $table->integer('jumlah_nasabah')->default(0);
            $table->string('nomor_telepon');
            $table->string('gambar_bsu')->nullable();
            $table->enum('reward_level', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->decimal('total_sampah', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_sampah_unit');
    }
};
