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
        Schema::table('mikrosxemas', function (Blueprint $table) {
            $table->dropColumn('biriktirilgan_shaxs');
        });
    }

    public function down(): void
    {
        Schema::table('mikrosxemas', function (Blueprint $table) {
            $table->string('biriktirilgan_shaxs')->after('biriktirilgan_joyi');
        });
    }
};
