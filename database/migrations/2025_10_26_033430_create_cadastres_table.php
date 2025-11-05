<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cadastres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Bino va inshootning nomi
            $table->string('cadastre_number'); // Kadastr raqami
            $table->integer('floors_count')->nullable(); // Qavat soni
            $table->decimal('construction_area', 10, 2)->nullable(); // Qurilish osti maydoni (kv.m)
            $table->decimal('total_area', 10, 2)->nullable(); // Umumiy maydoni (kv.m)
            $table->decimal('useful_area', 10, 2)->nullable(); // Umumiy foydali maydoni (kv.m)
            $table->json('details')->nullable(); // Qo'shimcha ma'lumotlar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cadastres');
    }
};