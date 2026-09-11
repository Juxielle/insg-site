<tr data-index="{{ $index }}">
@foreach(['last_name' => ['Nom',100], 'first_names' => ['Prénom',150], 'field' => ['Filière',150]] as $key => [$label, $max])
<td><input class="form-control" style="min-width:140px" name="entries[{{ $index }}][{{ $key }}]" aria-label="{{ $label }}" value="{{ $entry[$key] ?? '' }}" maxlength="{{ $max }}" required></td>
@endforeach
<td><input class="form-control" style="min-width:100px" type="number" name="entries[{{ $index }}][average]" aria-label="Moyenne" min="0" max="20" step="0.01" value="{{ $entry['average'] ?? '' }}" required></td>
<td><input class="form-control" style="min-width:140px" name="entries[{{ $index }}][decision]" aria-label="Décision" maxlength="100" placeholder="Admis, non admis…" value="{{ $entry['decision'] ?? '' }}" required></td>
<td><input class="form-control" style="min-width:80px" type="number" name="entries[{{ $index }}][rank]" aria-label="Rang" min="1" max="2147483647" value="{{ $entry['rank'] ?? '' }}" required></td>
<td><button type="button" class="btn btn-outline-danger btn-sm" data-remove aria-label="Supprimer cet étudiant">Retirer</button></td>
</tr>