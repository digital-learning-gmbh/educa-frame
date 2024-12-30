@extends('verwaltung.schulerakte.main')

@section('siteContent')
    <div class="col-md-12">
        <div class="card">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <a class="navbar-brand" href="#">Dokumente</a>
                    <ul class="navbar-nav mr-auto">
                        <!-- <li class="nav-item active">
                            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Link</a>
                        </li> -->
                            <li class="nav-item active">
                                <a class="nav-link" href="/verwaltung/schulerlisten/{{ $schuler->id }}/dokumente">
                                    Dateien
                                </a>
                            </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/verwaltung/schulerlisten/{{ $schuler->id }}/dokumente?formular">
                                Formulare
                            </a>
                        </li>
                    </ul>

                </div>
            </nav>
            <div class="card-body" id="dokumentCard">
                <p>Hier gibt es die Möglichkeit, Dokumente an die Schülerdatei zu hängen.</p>
                @component('documents.list',[ "model" => $schuler, "type" => "schuler"])
                @endcomponent
            </div>
        </div>
    </div>
@endsection

