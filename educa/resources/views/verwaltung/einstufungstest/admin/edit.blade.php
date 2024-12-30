@extends('verwaltung.einstufungstest.admin.main')


@section('siteContent')
    <h2>Test bearbeiten</h2>
    <form class="form-horizontal" method="POST">

        <div class="card">
            <div class="card-body">
                @csrf
                <div class="card-title"><h2>{{ $test->name }}</h2></div>
                <div class="form-group">
                    <label for="email" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        <input name="name" type="text" class="form-control" id="email" placeholder="Name" value="{{ $test->name  }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="public" class="col-sm-2 control-label">Öffentlich verfügbar</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <input name="public" id="checkbox1" type="checkbox" @if($test->public == true) checked @endif>
                            <label for="checkbox1" >verfügbar</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="col-sm-2 control-label">E-Mail (an diese E-Mail  werden die Ergebnisse geschickt)</label>
                    <div class="col-sm-10">
                        <input name="email" type="email" class="form-control" id="email" placeholder="E-Mail" value="{{ $test->email  }}" required>
                    </div>
                </div>
            <div class="form-group">
                <label for="firstname" class="col-sm-2 control-label">Beschreibung</label>
                <div class="col-sm-10">
                    <textarea name="beschreibung" type="text" class="form-control" id="firstname" required>{{ $test->beschreibung  }}</textarea>
                </div>
            </div>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body">
                <div class="card-title"><h4>Bewertung</h4><a  href="#" onclick="addBewertung();" class="btn btn-success">Bewertungsoption hinzufügen</a></div>
                <p>Jede richtige Lücke gibt einen Punkt.</p>
                <div id="bewertungen">
                    @php $counter2 = 0; @endphp
                    @foreach($test->marks as $marks)
                        <div class="form-group row"  id="bewertung_{{ $counter2 }}">
                            <div class="col-sm-4">
                                <input name="marks_bezeichnung[]" type="text" class="form-control" placeholder="Bezeichnung" value="{{ $marks->bezeichnung  }}">
                            </div>
                            <div class="col-sm-4">
                                <input name="marks_minScore[]" type="number" class="form-control" placeholder="minimale Punktzahl" value="{{ $marks->minScore  }}">
                            </div>
                            <div class="col-sm-4"><a href="#" onclick="deleteBewertung('{{ $counter2 }}')" class="btn btn-danger">Entfernen</a></div>
                        </div>

                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body">
                <div class="card-title"><h4>Test-Inhalt</h4></div>
                <div id="aufgaben">
                    @php $counter = 0; @endphp
                @foreach($test->aufgaben as $aufgabe)
                    <div id="{{ $counter+1 }}">
                        <h6>Aufgabe {{ $counter+1 }} <a href="#" class="btn btn-xs btn-danger" onclick="deleteAufgabe('{{ $counter+1 }}')">Löschen</a></h6>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">Titel der Aufgabe</label>
                            <div class="col-sm-10">
                                <input name="title[]" type="text" class="form-control" id="email" placeholder="Name" value="{{ $aufgabe->title  }}" required>
                            </div>
                        </div>
            <div class="form-group">
                <label for="firstname" class="col-sm-2 control-label">Aufgabe {{ $counter+1 }}</label>
                <div class="col-sm-10">
                    <textarea name="aufgabe[]" type="text" class="form-control" id="firstname" rows="10" required>{{ $aufgabe->aufgabe  }}</textarea>
                </div>
            </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">Zusätzliche Auswahlmöglichkeiten</label>
                        <div class="col-sm-10">
                            <input name="gabsAdditional[]" type="text" class="form-control" id="email" placeholder="Name" value="{{ $aufgabe->gabsAdditional  }}">
                        </div>
                    </div>
                    </div>
                    @php $counter++ @endphp
                    @endforeach
                </div>
                <div class="form-group">
                <label for="email" class="col-sm-2 control-label"></label>
                <div class="col-sm-10">
                    <a class="btn btn-primary" style="float: right;" onclick="addAufgabe()">Weitere Aufgabe hinzufügen</a>
                </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-success" type="submit" style="float: right; margin-left: 5px;">Speichern</button>
                <a class="btn btn-danger" href="/verwaltung/einstufungstest/test/{{ $test->id }}/delete" style="float: right; margin-left: 5px;">Löschen</a>
                <div class="clearfix"></div>
            </div>
        </div>
    </form>
@endsection

@section('additionalScript')
<script>
    var counter = {{ $counter }};
    function addAufgabe() {
        document.getElementById("aufgaben").append(createElementFromHTML('' +
            '                            <div id="' + (counter+1) + '">' +
            '                <h6>Aufgabe ' + (counter+1) + '</h6>  ' +
            '             <div class="form-group">\n' +
            '                            <label for="email" class="col-sm-2 control-label">Titel der Aufgabe</label>\n' +
            '                            <div class="col-sm-10">\n' +
            '                                <input name="title[]" type="text" class="form-control" id="email" placeholder="Name" value="" required>\n' +
            '                            </div>\n' +
            '                        </div>' +
            '            <div class="form-group">\n' +
            '                <label for="firstname" class="col-sm-2 control-label">Aufgabe ' + (counter+1) + '</label>\n' +
            '                <div class="col-sm-10">\n' +
            '                    <textarea name="aufgabe[]" type="text" class="form-control" id="firstname" rows="10" required></textarea>\n' +
            '                </div>\n' +
            '            </div>\n' +
            '                    <div class="form-group">\n' +
            '                        <label for="email" class="col-sm-2 control-label">Zusätzliche Auswahlmöglichkeiten</label>\n' +
            '                        <div class="col-sm-10">\n' +
            '                            <input name="gabsAdditional[]" type="text" class="form-control" id="email" placeholder="Name" value="">\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                    </div>'));
        counter++;
    }
    function deleteAufgabe(id) {
        document.getElementById(id).remove();

    }
    function createElementFromHTML(htmlString) {
        var div = document.createElement('div');
        div.innerHTML = htmlString.trim();

        // Change this to div.childNodes to support multiple top-level nodes
        return div.firstChild;
    }

    var counter2 = {{ $counter2 }};
    function addBewertung()
    {
        document.getElementById("bewertungen").append(createElementFromHTML('                      <div id="bewertung_' + counter2 + '" class="form-group row">' +
            '            <div class="col-sm-4">' +
            '        <input name="marks_bezeichnung[]" type="text" class="form-control" placeholder="Bezeichnung" required>' +
        '        </div>' +
        '        <div class="col-sm-4">' +
    '            <input name="marks_minScore[]" type="number" class="form-control" placeholder="minimale Punktzahl" required>' +
    '        </div> <div class="col-sm-4"><a href="#" onclick="deleteBewertung(' + counter2+ ')" class="btn btn-danger">Entfernen</a></div>' +
    '    </div>'));
        counter2++;
    }

    function deleteBewertung(id)
    {
        document.getElementById("bewertung_" + id).remove();
    }
</script>
@endsection
