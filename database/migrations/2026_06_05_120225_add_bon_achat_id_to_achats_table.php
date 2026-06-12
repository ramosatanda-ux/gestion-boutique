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
        Schema::table('achats', function (Blueprint $table) {
            $table->foreignId('bon_achat_id')->nullable()->after('id')
                  ->constrained('bon_achats')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('achats', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\BonAchat::class);
            $table->dropColumn('bon_achat_id');
        });
    }
};
