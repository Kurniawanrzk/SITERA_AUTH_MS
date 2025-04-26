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
        Schema::create('nasabah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');            
            $table->string('nik')->unique();
            $table->string('nama');
            $table->text('alamat');
            $table->string('nomor_wa');
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_pemilik_rekening')->nullable();
            $table->string('jenis_rekening')->nullable();
            $table->enum('reward_level', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->decimal('total_sampah', 10, 2)->default(0);
            $table->decimal('saldo', 10, 2)->default(0);
            $table->uuid('bsu_id');
            $table->foreign('bsu_id')->references('id')->on('bank_sampah_unit')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nasabah');
    }
};
