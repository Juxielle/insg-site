<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><title>Résultats — {{ $contest->reference }} — Tour {{ $round }}</title>
<style>
@page { margin: 32px 30px 40px; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #17352c; }
h1 { font-size: 19px; margin: 8px 0; }
h2 { font-size: 14px; margin: 8px 0; }
.muted { color: #63736d; } .status { font-weight: bold; margin: 14px 0; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; }
thead { display: table-header-group; }
tr { page-break-inside: avoid; }
th { background: #174b3c; color: white; text-align: left; }
th, td { border: 1px solid #d7e0db; padding: 7px; overflow-wrap: break-word; }
tbody tr:nth-child(even) { background: #f3f7f5; }
.number { text-align: center; }
</style></head><body>
<strong>INSTITUT NATIONAL DES SCIENCES DE GESTION</strong>
<h1>{{ $contest->title }}</h1>
<h2>Résultats du {{ $round === 1 ? 'premier' : 'deuxième' }} tour</h2>
<p class="muted">{{ $contest->reference }} · {{ $contest->session }} · Année académique {{ $contest->academic_year }}</p>
<p class="status">{{ $published ? 'Résultats publiés le '.\Illuminate\Support\Carbon::parse($published)->format('d/m/Y') : 'BROUILLON — Résultats non publiés' }} · {{ $entries->count() }} étudiant(s)</p>
<table><thead><tr><th style="width:6%">Rang</th><th style="width:12%">Matricule</th><th style="width:12%">Date de naissance</th><th style="width:14%">Nom</th><th style="width:14%">Prénom</th><th style="width:16%">Filière</th><th style="width:10%">Moyenne / 20</th><th style="width:16%">Décision</th></tr></thead><tbody>
@forelse($entries as $entry)<tr><td class="number">{{ $entry->rank }}</td><td>{{ $entry->registration_number ?: '—' }}</td><td>{{ $entry->birth_date?->format('d/m/Y') ?: '—' }}</td><td>{{ $entry->last_name }}</td><td>{{ $entry->first_names }}</td><td>{{ $entry->field }}</td><td class="number">{{ $entry->average === null ? '—' : number_format((float) $entry->average, 2, ',', ' ') }}</td><td>{{ $entry->decision }}</td></tr>@empty<tr><td colspan="8">Aucun étudiant dans ce tour.</td></tr>@endforelse
</tbody></table>
</body></html>
