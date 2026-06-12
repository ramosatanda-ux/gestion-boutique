<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // vente_id déjà inclus dans 015913_create_vente_items_table
    if (Schema::hasTable('vente_items') && !Schema::hasColumn('vente_items', 'vente_id')) {
        Schema::table('vente_items', function (Blueprint $table) {
            $table->foreignId('vente_id')->constrained()->onDelete('cascade');
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vente_items', function (Blueprint $table) {
            //
        });
    }
};
