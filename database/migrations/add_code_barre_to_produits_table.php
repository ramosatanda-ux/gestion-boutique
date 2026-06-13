<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Commande : php artisan make:migration add_code_barre_to_produits_table
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('produits', 'code_barre')) {
            Schema::table('produits', function (Blueprint $table) {
                $table->string('code_barre')->unique()->nullable()->after('nom');
            });
        }
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn('code_barre');
        });
    }
};
