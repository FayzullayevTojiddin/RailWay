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
        Schema::create('branch_railways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained('stations')->onDelete('cascade');
            $table->string('name'); // Firma nomi
            $table->string('stir'); // STIR raqami
            $table->string('image')->nullable(); // Rasm
            $table->decimal('length', 10, 2); // Uzunligi (m)
            $table->year('established_year')->nullable(); // Tashkil etilgan yil
            $table->date('cancelled_at')->nullable(); // Bekor qilgan sana
            $table->json('details')->nullable(); // Qo'shimcha ma'lumotlar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_railways');
    }
};
