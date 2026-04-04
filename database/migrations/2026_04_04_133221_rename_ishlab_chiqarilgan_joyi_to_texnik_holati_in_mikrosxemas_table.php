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
            $table->renameColumn('ishlab_chiqarilgan_joyi', 'texnik_holati');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mikrosxemas', function (Blueprint $table) {
            $table->renameColumn('texnik_holati', 'ishlab_chiqarilgan_joyi');
        });
    }
};
