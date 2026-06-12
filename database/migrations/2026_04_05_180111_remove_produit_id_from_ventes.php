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
    Schema::table('ventes', function (Blueprint $table) {
        $table->dropForeign(['produit_id']); // 🔥 supprimer relation
        $table->dropColumn('produit_id');    // 🔥 supprimer colonne
    });
}

public function down()
{
    Schema::table('ventes', function (Blueprint $table) {
        $table->unsignedBigInteger('produit_id')->nullable();
    });
}
};
