<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avtomobils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->string('rusumi');
            $table->string('davlat_raqami');
            $table->year('ishlab_chiqarilgan_yili');
            $table->string('biriktirilgan_shaxs');
            $table->json('rasmlar')->nullable();
            $table->string('texpassport_old')->nullable();
            $table->string('texpassport_orqa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avtomobils');
    }
};
