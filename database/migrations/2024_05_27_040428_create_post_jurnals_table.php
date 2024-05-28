<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_jurnals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->string('judul');
            $table->string('deskripsi');
            $table->date('tanggal_dibuat');
            $table->string('input_mood');
            $table->string('image')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_jurnals');
    }
};
