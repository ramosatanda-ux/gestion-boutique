<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produit;

// Commande : php artisan barcodes:generer
class GenererCodeBarres extends Command
{
    protected $signature   = 'barcodes:generer';
    protected $description = 'Génère un code-barres EAN-13 pour chaque produit qui n\'en a pas';

    public function handle(): void
    {
        $produits = Produit::whereNull('code_barre')->orWhere('code_barre', '')->get();

        if ($produits->isEmpty()) {
            $this->info('✅ Tous les produits ont déjà un code-barres.');
            return;
        }

        $this->info("🔄 Génération pour {$produits->count()} produit(s)...");

        foreach ($produits as $produit) {
            $produit->code_barre = self::genererEan13($produit->id);
            $produit->save();
            $this->line("  ✔ {$produit->nom} → {$produit->code_barre}");
        }

        $this->info('✅ Codes-barres générés avec succès.');
    }

    // Génère un EAN-13 valide à partir d'un id produit
    public static function genererEan13(int $id): string
    {
        // Préfixe pays fictif "200" (usage interne) + id sur 9 chiffres
        $base = '200' . str_pad($id, 9, '0', STR_PAD_LEFT);

        // Calcul de la clé de contrôle EAN-13
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$base[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        $cle = (10 - ($sum % 10)) % 10;

        return $base . $cle;
    }
}
