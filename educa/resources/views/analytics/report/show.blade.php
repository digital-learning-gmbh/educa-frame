@extends('analytics.report')

@section('cloudContent')
    <style>
        .dtsb-searchBuilder {
            border: 1px solid rgba(0,0,0,.125);
            border-radius: .25rem;
            padding: 5px;
        }
    </style>
    <h2>{{ $report->getConfig()->name }}</h2>
    @php $nextStep = false; @endphp
    @if($report->hasParams())
        <div class="card mb-5">
            <div class="card-body">
                <h5 class="card-title"><b><i class="fas fa-sliders-h"></i> Parameter</b></h5>
                <form method="POST">
                    @csrf
                    @php $i = 0;  @endphp
                    @foreach($report->getParams() as $param)
                        @if(property_exists($param,"requires") && !request()->has($param->requires))
                            @php $nextStep = true; continue;  @endphp
                        @endif
                        @if($param->type == "select" || $param->type == "selectmultiple")
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">{{ $param->name }}</label>
                                <div class="col-sm-10">
                                    @if($param->type == "selectmultiple")
                                        <select name="{{ $param->sql }}[]" class="select2" required multiple id="select_{{ $i }}">
                                        @else
                                        <select name="{{ $param->sql }}" class="select2">
                                    @endif
                                                    @foreach($param->name == "Schule" ? (
                                                                    $cloud_user->administrationUser() != null ? $cloud_user->administrationUser()->schulen :
                                                                                ($cloud_user->dozentUser() != null ? $cloud_user->dozentUser()->schulen :  [] ) )
                                                                                : DB::select(str_replace(":".$param->requires,request()->input($param->requires), $param->selector)) as $option)
                                                        @if($param->type == "selectmultiple")
                                                            <option value="{{ $option->id }}" @if(isset($params) && is_array($params->input($param->sql)) && in_array($option->id, $params->input($param->sql))) selected @endif>{{ $option->name }}</option>
                                                        @else
                                                            <option value="{{ $option->id }}" @if(isset($params) && $params->input($param->sql) == $option->id) selected @endif>{{ $option->name }}</option>
                                                        @endif
                                                    @endforeach
                                    </select>

                                        @if($param->type == "selectmultiple")
                                                    <a href="#" onclick='$("#select_{{ $i }} option").attr("selected", true).parent().trigger("change");' class="btn btn-secondary m-1">Alles auswählen</a>
                                        @endif
                                </div>
                            </div>
                        @elseif($param->type == "datetime")
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">{{ $param->name }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group date" id="analyticspicker_{{ $i }}" data-target-input="nearest">
                                        <input value="@if(isset($params) && $params->has($param->sql)) {{ $params->input($param->sql)  }} @else {{ date("d.m.Y H:i",strtotime($param->default)) }} @endif" id="inputDate_{{ $i }}" name="{{ $param->sql }}" type="text" class="form-control datetimepicker-input tobeHidden" data-target="#analyticspicker_{{ $i }}" required/>
                                        <div class="input-group-append" data-target="#analyticspicker_{{ $i }}" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($param->type == "date")
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">{{ $param->name }}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group date" id="analyticspicker_{{ $i }}" data-target-input="nearest">
                                            <input value="@if(isset($params) && $params->has($param->sql)) {{ $params->input($param->sql)  }} @else {{ date("d.m.Y",strtotime($param->default)) }} @endif" id="inputDate_{{ $i }}" name="{{ $param->sql }}" type="text" class="form-control datetimepicker-input tobeHidden" data-target="#analyticspicker_{{ $i }}" required/>
                                            <div class="input-group-append" data-target="#analyticspicker_{{ $i }}" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @else
                            <div class="alert alert-info">Dieser Parameter wird nicht unterstützt. Bitte aktualiseren Sie Analytics.</div>
                        @endif
                        @php $i++ @endphp
                    @endforeach
                    @if($nextStep)
                        <input name="execute" type="hidden" value="false">
                        <button style="float: right;" class="btn btn-primary">Weiter</button>
                        @else
                        @if($report->getConfig()->long_running)
                            <p class="alert alert-info">Dieser Report aggeriert viele Daten, daher wird der Report im Hintergrund ausgeführt.</p>
                            <button style="float: right;" class="btn btn-primary">Ausführen</button>
                            @else
                    <button style="float: right;" class="btn btn-primary">Ausführen</button>
                            @endif
                        @endif
                </form>
            </div>
        </div>
    @endif


    @if((!$report->hasParams() || isset($params)) && !request()->has("execute"))
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><b><i class="fas fa-table"></i> Report-Ergebnis</b></h5>
                <div class="overflow-auto">
                    <table id="report_tabelle_{{ $report->id }}" class="table table-striped table-bordered" style="width 100%; margin-top: 0px !important;">
                        <thead>
                        <tr>
                            @foreach($report->getColumns() as $column)
                                <th>{{ $column->name }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="dataEditModal"   role="dialog" aria-labelledby="dataEditModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataEditModalLabel">... bearbeiten</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/analytics/report/{{ $report->id }}/update">
                    @csrf
                    @if(isset($params))
                        @foreach($params->all() as $key=>$value)
                            @if($key != "_token")
                                @if(is_array($value))
                                    @foreach($value as $subvalue)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $subvalue }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endif
                        @endforeach
                    @endif
                    <div class="modal-body">
                        <input id="dataEditModalFieldName" type="hidden" name="fieldName" value="">
                        <div id="dataEditModalContainer">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('additionalScript')
    <script src="https://cdn.datatables.net/datetime/1.1.1/js/dataTables.dateTime.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
    <script>
        $(document).ready(function() {
            $.fn.dataTable.moment(['DD.MM.YYYY','HH:mm']);
            //console.log("test teilnehmer widget js");
            var reportTable = $('#report_tabelle_{{ $report->id }}').DataTable({
                ajax:
                    {
                        "url": '/analytics/report/ajax/{{ $report->id }}',
                        "data": {
                            @if(isset($params))
                                @foreach($report->getParams() as $param)
                                @if($param->type == "selectmultiple")
                                @if(is_array($params->input($param->sql)))
                            "{{ $param->sql }}" : "{{ implode(",",$params->input($param->sql)) }}",
                            @else
                            "{{ $param->sql }}" : "[]",
                            @endif
                                @else
                            "{{ $param->sql }}" : "{{ $params->input($param->sql) }}",
                            @endif
                            @endforeach
                            @endif
                        }
                    },
                language : {
                    url: "/js/german.json",
                    searchBuilder: {
                        add: '+ Bedingung hinzufügen',
                        condition: 'Bedingung',
                        conditions :{
                            any: {
                                contains: 'Beinhaltet',
                                empty: 'Leer',
                                endsWith: 'endet mit',
                                equals: 'ist',
                                not: 'nicht',
                                notEmpty: 'nicht leer',
                                startsWith: 'beginnt mit',
                            },
                            array: {
                                contains: 'Beinhaltet',
                                empty: 'Leer',
                                endsWith: 'endet mit',
                                equals: 'ist',
                                not: 'nicht',
                                notEmpty: 'nicht leer',
                                startsWith: 'beginnt mit',
                            },
                            string: {
                                contains: 'Beinhaltet',
                                empty: 'Leer',
                                endsWith: 'endet mit',
                                equals: 'ist',
                                not: 'nicht',
                                notEmpty: 'nicht leer',
                                startsWith: 'beginnt mit',
                            },
                            number: {
                                contains: 'Beinhaltet',
                                empty: 'Leer',
                                endsWith: 'endet mit',
                                equals: 'ist',
                                not: 'nicht',
                                notEmpty: 'nicht leer',
                                startsWith: 'beginnt mit',
                            },
                            date: {
                                contains: 'Beinhaltet',
                                empty: 'Leer',
                                endsWith: 'endet mit',
                                equals: 'ist',
                                not: 'nicht',
                                notEmpty: 'nicht leer',
                                startsWith: 'beginnt mit',
                                before: 'vor',
                                after: 'nach',
                                between: 'zwischen',
                                notBetween: 'nicht zwischen',
                                and: 'und'
                            }
                        },
                        clearAll: 'Zurücksetzen',
                        deleteTitle: 'Löschen',
                        data: 'Spalte',
                        leftTitle: 'Links',
                        logicAnd: 'und',
                        logicOr: 'oder',
                        rightTitle: 'Rechts',
                        title: {
                            0: 'Daten filtern',
                            _: 'Filter (%d)'
                        },
                        value: 'Option',
                    }
                },
                dom: 'QlfBrtip',
                buttons: [ 'print',{
                    filename: '{{ $report->getConfig()->name }}',
                    extend: 'excelHtml5',
                    customizeData: function ( data ) {
                        for (var i=0; i<data.body.length; i++){
                            for (var j=0; j<data.body[i].length; j++ ){
                                data.body[i][j] = data.body[i][j];
                            }
                        }
                    },
                    // customize: function( xlsx ) {
                    //     var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    //     var range = XLSX.utils.decode_range(sheet['!ref']); // get the range
                    //     for(var R = range.s.r; R <= range.e.r; ++R) {
                    //         for (var C = range.s.c; C <= range.e.c; ++C) {
                    //             console.log('Row : ' + R);
                    //             console.log('Column : ' + C);
                    //             var cellref = XLSX.utils.encode_cell({c:C, r:R}); // construct A1 reference for cell
                    //             if(!sheet[cellref]) continue; // if cell doesn't exist, move on
                    //             var cell = sheet[cellref];
                    //             cell.attr( 't', 's' );
                    //         }
                    //     }
                    // }
                }, 'pdf', 'colvis'
                    @if(count($report->getEditableColumns()) > 0)
                    ,{
                        extend: "collection",
                        autoClose: true,
                        text: "Bearbeiten",
                        buttons: [
                                @foreach($report->getEditableColumns() as $column)
                            {
                                text: "{{ $column->name }}",
                                action: function() {
                                    var editTitleFields = [
                                        @foreach($column->edittitle as $edittitle)
                                            "{{ $edittitle }}",
                                        @endforeach
                                    ]
                                    $('#dataEditModalContainer').html(""); //clear beforehand
                                    $('#dataEditModalLabel').html("{{ $column->name }} bearbeiten");
                                    $('#dataEditModalFieldName').val("{{ $column->sql }}");
                                    reportTable.rows( { filter : 'applied'} ).every( function ( rowIdx, tableLoop, rowLoop ) {
                                        var data = this.data();
                                        let titleString = "";
                                        editTitleFields.forEach(function(value){
                                            titleString += data[value] + " ";
                                        });
                                        let fieldFiller = "";
                                        if(data["{{ $column->sql }}"] != null)
                                            fieldFiller = data["{{ $column->sql }}"];

                                        @if(property_exists($column,"datatype") && $column->datatype == "date")
                                            fieldFiller = moment(fieldFiller,"DD-MM-YYYY").format("YYYY-MM-DD")
                                        @endif

                                        @if(property_exists($column,"datatype") && $column->datatype == "enum")
                                            @php
                                            $enum_values = explode(",",$column->dataenum_value);
                                            $enum_labels = explode(",",$column->dataenum_label);
                                            $i = 0;
                                            @endphp
                                        var options = "";
                                        @foreach($enum_values as $values)
                                            options += '<option value="{{ $values }}" ';
                                            if(fieldFiller == '{{ $enum_labels[$i] }}')
                                            {
                                                options += ' selected ';
                                            }
                                            options += '>{{ $enum_labels[$i] }}</option>';
                                            @php $i++; @endphp
                                            @endforeach
                                        $('#dataEditModalContainer').append('<div class="form-group row">' +
                                            '<label for="r' + rowIdx + '" class="col-sm-2 col-form-label">' + titleString + '</label>' +
                                            '<div class="col-sm-10"><select class="form-control" name="object_values[]"  id="r' + rowIdx + '">' +
                                            options +
                                            '</select><input type="hidden" name="object_ids[]" value="' + data["internalid"] + '">'
                                        );
                                        @else
                                        $('#dataEditModalContainer').append('<div class="form-group row">' +
                                            '<label for="r' + rowIdx + '" class="col-sm-2 col-form-label">' + titleString + '</label>' +
                                            '<div class="col-sm-10"><input type="{{ property_exists($column,"datatype") ? $column->datatype : "text" }}" class="form-control" id="r' + rowIdx + '" value="' + fieldFiller + '" name="object_values[]"></div>' +
                                            '<input type="hidden" name="object_ids[]" value="' + data["internalid"] + '">'
                                        );
                                        @endif
                                    } );
                                    $('#dataEditModal').modal('show');
                                }
                            },
                            @endforeach
                        ]
                    }
                    @endif
                ],
                colReorder: true,
                columns: [
                        @foreach($report->getColumns() as $column)
                    { data: '{{ $column->sql }}',
                        @if(property_exists($column,"type") && $column->type)   render: function(data){
                            if(data == null)
                                return null;
                            return data ;
                        }
                        @endif
                    },
                    @endforeach
                ]
            });
            window.reportTable = reportTable;
        });
    </script>


    @php $i = 0; @endphp
    @foreach($report->getParams() as $param)
        @if($param->type == "select" || $param->type == "selectmultiple")
            @if(property_exists($param,"reloadAuto") && $param->reloadAuto)

            @endif
        @elseif($param->type == "date")
            <script>
                $(document).ready(function() {
                    $('#analyticspicker_{{ $i }}').datetimepicker({
                        locale: 'de',
                        format: 'L'
                    });
                });
            </script>
        @endif
        @if($param->type == "datetime")
        <script>
            $(document).ready(function() {
                $('#analyticspicker_{{ $i }}').datetimepicker({
                    locale: 'de',
                });
            });
        </script>
        @endif


        @php $i++ @endphp
    @endforeach
@endsection

@section('title')
    {{ $report->getConfig()->name }} - Analytics
@endsection

@section('additionalStyle')
    <link href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css" rel="stylesheet">
@endsection
