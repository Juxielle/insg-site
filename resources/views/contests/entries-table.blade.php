<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Matricule</th><th>Nom et prénom</th><th>Date de naissance</th><th>Filière</th><th>Décision</th></tr></thead><tbody>
@forelse($entries as $entry)
<tr><td>{{ $entry->registration_number ?: '—' }}</td><td>{{ $entry->last_name }} {{ $entry->first_names }}</td><td>{{ $entry->birth_date?->format('d/m/Y') ?: '—' }}</td><td>{{ $entry->field }}</td><td><strong class="contest-decision {{ $entry->decision === 'Admis(e)' ? 'is-admitted' : 'is-adjourned' }}">{{ $entry->decision === 'Admis(e)' ? 'ADMIS(E)' : 'AJOURNÉ(E)' }}</strong></td></tr>
@empty<tr><td colspan="5" class="text-muted text-center">Aucun résultat publié ne correspond à votre recherche.</td></tr>@endforelse
</tbody></table></div>