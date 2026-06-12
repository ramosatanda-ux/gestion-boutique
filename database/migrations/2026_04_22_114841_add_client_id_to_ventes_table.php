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
        if (!Schema::hasColumn('ventes', 'client_id')) {
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
        }
        if (!Schema::hasColumn('ventes', 'est_credit')) {
            $table->boolean('est_credit')->default(false);
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            //
        });
    }
};
