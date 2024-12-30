
@section('pageContent')

    <div class="container gedf-wrapper">
                @if($activites->count() == 0)
                    <h6 class="text-center mt-1">Noch keine Aktivitäten vorhanden</h6>
                @endif
                @foreach($activites as $activite)
                <a href="{{ $activite->link }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $activite->creator_display() }}</h5>
                    <small>{{ $activite->created_at->diffForHumans() }}</small>
            </div>
                    <p class="mb-1"><b>{{ $activite->creator_display() }}</b> {{ $activite->content }}</p>
           </a>
                @endforeach
    </div>
@endsection

