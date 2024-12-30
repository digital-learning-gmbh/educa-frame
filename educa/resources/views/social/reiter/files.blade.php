
@section('pageContent')

    <div class="container gedf-wrapper">
        <div class="card">
            <div class="card-body">
    @component('documents.list',[ "model" => $group, "type" => "group"])
    @endcomponent
            </div></div>
    </div>
@endsection

