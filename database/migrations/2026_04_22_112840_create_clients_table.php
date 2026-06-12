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
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string('nom');
        $table->string('telephone')->nullable();
        $table->string('adresse')->nullable();

        // 🔥 logique métier
        $table->boolean('est_particulier')->default(false);
        $table->boolean('a_credit')->default(false);

        $table->decimal('solde', 10, 2)->default(0);

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
