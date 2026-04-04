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
        Schema::table('avtomobils', function (Blueprint $table) {
            $table->string('texnik_holati')->nullable()->after('biriktirilgan_shaxs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avtomobils', function (Blueprint $table) {
            $table->dropColumn('texnik_holati');
        });
    }
};
