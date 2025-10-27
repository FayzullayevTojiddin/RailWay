<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained('stations')->onDelete('cascade');
            $table->string('serial_number');
            $table->string('image')->nullable();
            $table->json('owner_details')->nullable();
            $table->json('wagon_distance')->nullable();
            $table->decimal('length', 10, 2);
            $table->json('license')->nullable();
            $table->string('technical_passport')->nullable();
            $table->string('direction')->nullable();
            $table->string('loading_joy')->nullable();
            $table->json('work_times')->nullable();
            $table->string('front')->nullable();
            $table->integer('wagon_counts')->default(0);
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rails');
    }
};