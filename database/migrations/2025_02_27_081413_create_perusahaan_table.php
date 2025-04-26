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
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');            
            // $table->string('nib_npwp')->unique();
            $table->string('nama_perusahaan');
            $table->string('jenis_perusahaan');
            $table->text('alamat');
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->string('email_bisnis');
            $table->string('nomor_telepon');
            $table->string('gambar_perusahaan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perusahaan');
    }
};
