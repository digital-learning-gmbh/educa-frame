@foreach($documents as $dokument)
    @if($dokument->isWord())
<tr class="treegrid-{{$dokument->id}} @if($dokument->parent_id != 0) treegrid-parent-{{$dokument->parent_id}}@endif">

        <td>{{ $dokument->name  }}</td>
    <td>{{ $dokument->file_type }}</td>
    <td>{{ $dokument->size }}</td>
    <td>{{ $dokument->created_at->diffForHumans() }}</td>
    <td>@if($dokument->owner_type == "user")
        {{ \App\User::find($dokument->owner_id)->DisplayName }}
        @elseif($dokument->owner_type == "lehrer")
        {{ \App\Lehrer::find($dokument->owner_id)->DisplayName }}
        @elseif($dokument->owner_type == "cloudid")
        {{ \App\CloudID::find($dokument->owner_id)->DisplayName }}
        @elseif($dokument->owner_type == "kontakt")
        {{ \App\Kontakt::find($dokument->owner_id)->DisplayName }}
        @endif
    </td>
    <td>
                <a href="/dokument/office/mailMerge?file={{ $dokument->id }}" target="_blank"  class="btn btn-xs btn-primary"><i class="fas fa-envelope-open-text"></i> Erstellung starten</a>

     </td>
</tr>
    @endif
@component('documents.childern_list',[ "documents" => $dokument->childern()])
@endcomponent
    @endforeach
