<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained('stations')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->string('full_name');
            $table->string('phone_number');
            $table->date('birth_date');
            $table->timestamp('joined_at');
            $table->string('document_type');
            $table->string('role');
            $table->enum('sex', ['male', 'female']);
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};