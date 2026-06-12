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
        Schema::create('vente_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('facture_id')->constrained()->onDelete('cascade');
    $table->foreignId('produit_id')->constrained();
    $table->integer('quantite');
    $table->integer('prix');
    $table->integer('total');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vente_items');
    }
};
