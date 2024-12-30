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
                            <li class="nav-item ">
                                <a class="nav-link" href="/verwaltung/schulerlisten/{{ $schuler->id }}/dokumente">
                                    Dateien
                                </a>
                            </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="/verwaltung/schulerlisten/{{ $schuler->id }}/dokumente?formular">
                                Formulare
                            </a>
                        </li>
                    </ul>

                </div>
            </nav>
            <div class="card-body" id="dokumentCard">
                <p>Hier befinden sich ausgefüllte Dokumente, die diesen Datensatz betreffen.</p>
                <table id="table_id" class="data-table table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Formularname</th>
                        <th>Unternehmen</th>
                        <th>Datum</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($geschickte as $geschickt)
                        <tr>
                            <td>{{ $geschickt->formularRevision->formular->name }}</td>
                            <td>{{ $geschickt->attr('creator')->name }}</td>
                            <td>{{ $geschickt->created_at }}</td>
                            <td>
                                <a target="_blank" href="/formular/{{ $geschickt->id }}/view" class="btn btn-xs btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/formular/{{ $geschickt->id }}/delete" class="btn btn-xs btn-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

