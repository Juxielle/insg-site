<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['institution_name', 'Nom de l’institution', 'Institut National des Sciences de Gestion', 'text'],
            ['institution_short_name', 'Nom abrégé', 'INSG', 'text'],
            ['footer_description', 'Présentation dans le pied de page', 'L’Institut National des Sciences de Gestion forme les cadres et managers qui contribuent à la performance des organisations gabonaises.', 'textarea'],
            ['address', 'Adresse', 'Boulevard Triomphal, Libreville, Gabon', 'text'],
            ['phone', 'Téléphone', '+241 01 23 45 67', 'text'],
            ['email', 'Adresse email', 'contact@insg-gabon.ga', 'email'],
            ['copyright', 'Mention de copyright', 'Institut National des Sciences de Gestion — Tous droits réservés.', 'text'],
        ] as [$key, $label, $value, $type]) {
            SiteSetting::updateOrCreate(['key' => $key], compact('label', 'value', 'type'));
        }
    }
}
