<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Codes-barres produits</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: white; }

        h1 {
            text-align: center;
            font-size: 13px;
            color: #333;
            margin: 8px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #ccc;
        }

        /* Table principale : 3 étiquettes par ligne */
        .grille { width: 100%; border-collapse: separate; border-spacing: 8px; }
        .grille td { width: 33%; vertical-align: top; }

        /* Étiquette individuelle */
        .etiquette {
            border: 1px solid #aaa;
            border-radius: 6px;
            padding: 8px 6px 6px;
            text-align: center;
            background: white;
            page-break-inside: avoid;
        }

        .nom  { font-size: 8px; font-weight: bold; color: #111; margin-bottom: 3px; }
        .prix { font-size: 10px; font-weight: bold; color: #16a34a; margin-bottom: 6px; }

        /* Table de barres : chaque cellule = 1 module */
        .barres-table {
            border-collapse: collapse;
            margin: 0 auto 4px;
            /* 95 modules × 1.4px = ~133px */
            width: 133px;
            height: 50px;
        }
        .barres-table td {
            padding: 0;
            margin: 0;
            width: 1.4px;
            height: 50px;
        }
        /* Barre normale */
        .n1 { background: #000; width: 1.4px; }
        .n0 { background: #fff; width: 1.4px; }
        /* Garde (plus haute de 5px) */
        .g1 { background: #000; width: 1.4px; height: 55px; }
        .g0 { background: #fff; width: 1.4px; height: 55px; }

        .code-num {
            font-size: 7px;
            letter-spacing: 1.5px;
            color: #222;
            font-family: monospace;
            margin-top: 2px;
        }

        @page { margin: 8mm; size: A4 portrait; }
    </style>
</head>
<body>

<h1>Codes-barres — {{ now()->format('d/m/Y') }}</h1>

@php
function calcBits(string $code): string {
    $enc = [
        'L' => ['0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'],
        'G' => ['0100111','0110011','0011011','0100001','0011101','0111001','0000101','0010001','0001001','0010111'],
        'R' => ['1110010','1100110','1101100','1000010','1011100','1001110','1010000','1000100','1001000','1110100'],
    ];
    $pt = [0=>'LLLLLL',1=>'LLGLGG',2=>'LLGGLG',3=>'LLGGGL',
           4=>'LGLLGG',5=>'LGGLLG',6=>'LGGGLL',7=>'LGLGLG',
           8=>'LGLGGL',9=>'LGGLGL'];
    $p = $pt[(int)$code[0]];
    $b = '101';
    for ($i = 1; $i <= 6; $i++) $b .= $enc[$p[$i-1]][(int)$code[$i]];
    $b .= '01010';
    for ($i = 7; $i <= 12; $i++) $b .= $enc['R'][(int)$code[$i]];
    return $b . '101';
}

// Positions des gardes (plus hautes) : début(0-2), centre(45-49), fin(92-94)
$gardes = array_flip(array_merge(range(0,2), range(45,49), range(92,94)));

$chunks = $produits->chunk(3);
@endphp

<table class="grille">
@foreach($chunks as $ligne)
<tr>
    @foreach($ligne as $produit)
    @php
        $code = $produit->code_barre;
        $bits = calcBits($code);
        $len  = strlen($bits); // 95
    @endphp
    <td>
        <div class="etiquette">
            <div class="nom">{{ strtoupper($produit->nom) }}</div>
            <div class="prix">{{ number_format($produit->prix, 0, ',', ' ') }} FCFA</div>

            {{-- Table des barres : 1 colonne par module --}}
            <table class="barres-table" cellspacing="0" cellpadding="0">
                <tr>
                @for($i = 0; $i < $len; $i++)
                    @php
                        $garde = isset($gardes[$i]);
                        $noir  = $bits[$i] === '1';
                        $cls   = $garde ? ($noir ? 'g1' : 'g0') : ($noir ? 'n1' : 'n0');
                    @endphp
                    <td class="{{ $cls }}"></td>
                @endfor
                </tr>
            </table>

            <div class="code-num">
                {{ $code[0] }} {{ substr($code,1,6) }} {{ substr($code,7,6) }}
            </div>
        </div>
    </td>
    @endforeach

    {{-- Cellules vides si ligne incomplète --}}
    @for($j = $ligne->count(); $j < 3; $j++)
        <td></td>
    @endfor
</tr>
@endforeach
</table>

</body>
</html>