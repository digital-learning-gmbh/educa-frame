@foreach($documents as $dokument)
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
            @if($dokument->isWord())
                <a href="/dokument/office?file={{ $dokument->id }}" target="_blank"  class="btn btn-xs btn-primary"><i class="far fa-file-word"></i> Word öffnen</a>
            @else
            @if($dokument->supportPreview())
                <a href="#" onclick="loadPreview({{ $dokument->id }})" class="btn btn-xs btn-primary"><i class="far fa-eye"></i></a>
            @endif
                @endif
        @if($dokument->type == "file")
            <a href="/dokument/{{ $dokument->id }}/download" class="btn btn-xs btn-secondary"><i class="fas fa-file-download"></i></a>
        @endif
        <a href="#" onclick="setId({{$dokument->id}})" data-toggle="modal" data-target="#moveModal" class="btn btn-xs btn-info"><i class="fas fa-exchange-alt fa-inverse"></i></a>
        <a href="/dokument/{{ $dokument->id }}/delete" class="btn btn-xs btn-danger"><i class="fas fa-trash-alt"></i></a>
    </td>
</tr>
@component('documents.childern_list',[ "documents" => $dokument->childern()])
@endcomponent
    @endforeach
