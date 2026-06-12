<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// NOUVELLE MIGRATION à créer : php artisan make:migration add_stock_minimum_to_produits_table
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            // Alerte stock minimum (défaut : 5 unités)
            $table->integer('stock_minimum')->default(5)->after('quantite');
            // Description optionnelle
            $table->string('description')->nullable()->after('nom');
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn(['stock_minimum', 'description']);
        });
    }
};
