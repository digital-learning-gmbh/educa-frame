<html>
<head>
    <style>
        body {
            font-size: 10px;
        }
        /** Define the margins of your page **/
        @page {
            margin: 100px 50px;
        }


        p {
            margin: 10px 0;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;

            /** Extra personal styles **/
            border-bottom: 2px solid #00488e;
            color: black;
            text-align: left;
            line-height: 35px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: right;
        }
        h1, h2, h3 {
            color: #00488e;
        }


        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }

        .table .table {
            background-color: #fff;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table-bordered thead th,
        .table-bordered thead td {
            border-bottom-width: 2px;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-primary,
        .table-primary > th,
        .table-primary > td {
            background-color: #b8daff;
        }

        .table-hover .table-primary:hover {
            background-color: #9fcdff;
        }

        .table-hover .table-primary:hover > td,
        .table-hover .table-primary:hover > th {
            background-color: #9fcdff;
        }

        .table-secondary,
        .table-secondary > th,
        .table-secondary > td {
            background-color: #d6d8db;
        }

        .table-hover .table-secondary:hover {
            background-color: #c8cbcf;
        }

        .table-hover .table-secondary:hover > td,
        .table-hover .table-secondary:hover > th {
            background-color: #c8cbcf;
        }

        .table-success,
        .table-success > th,
        .table-success > td {
            background-color: #c3e6cb;
        }

        .table-hover .table-success:hover {
            background-color: #b1dfbb;
        }

        .table-hover .table-success:hover > td,
        .table-hover .table-success:hover > th {
            background-color: #b1dfbb;
        }

        .table-info,
        .table-info > th,
        .table-info > td {
            background-color: #bee5eb;
        }

        .table-hover .table-info:hover {
            background-color: #abdde5;
        }

        .table-hover .table-info:hover > td,
        .table-hover .table-info:hover > th {
            background-color: #abdde5;
        }

        .table-warning,
        .table-warning > th,
        .table-warning > td {
            background-color: #ffeeba;
        }

        .table-hover .table-warning:hover {
            background-color: #ffe8a1;
        }

        .table-hover .table-warning:hover > td,
        .table-hover .table-warning:hover > th {
            background-color: #ffe8a1;
        }

        .table-danger,
        .table-danger > th,
        .table-danger > td {
            background-color: #f5c6cb;
        }

        .table-hover .table-danger:hover {
            background-color: #f1b0b7;
        }

        .table-hover .table-danger:hover > td,
        .table-hover .table-danger:hover > th {
            background-color: #f1b0b7;
        }

        .table-light,
        .table-light > th,
        .table-light > td {
            background-color: #fdfdfe;
        }

        .table-hover .table-light:hover {
            background-color: #ececf6;
        }

        .table-hover .table-light:hover > td,
        .table-hover .table-light:hover > th {
            background-color: #ececf6;
        }

        .table-dark,
        .table-dark > th,
        .table-dark > td {
            background-color: #c6c8ca;
        }

        .table-hover .table-dark:hover {
            background-color: #b9bbbe;
        }

        .table-hover .table-dark:hover > td,
        .table-hover .table-dark:hover > th {
            background-color: #b9bbbe;
        }

        .table-active,
        .table-active > th,
        .table-active > td {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-hover .table-active:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-hover .table-active:hover > td,
        .table-hover .table-active:hover > th {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table .thead-dark th {
            color: #fff;
            background-color: #212529;
            border-color: #32383e;
        }

        .table .thead-light th {
            color: #495057;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .table-dark {
            color: #fff;
            background-color: #212529;
        }

        .table-dark th,
        .table-dark td,
        .table-dark thead th {
            border-color: #32383e;
        }

        .table-dark.table-bordered {
            border: 0;
        }

        .table-dark.table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .table-dark.table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.075);
        }

        @media (max-width: 575.98px) {
            .table-responsive-sm {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: -ms-autohiding-scrollbar;
            }
            .table-responsive-sm > .table-bordered {
                border: 0;
            }
        }

        @media (max-width: 767.98px) {
            .table-responsive-md {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: -ms-autohiding-scrollbar;
            }
            .table-responsive-md > .table-bordered {
                border: 0;
            }
        }

        @media (max-width: 991.98px) {
            .table-responsive-lg {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: -ms-autohiding-scrollbar;
            }
            .table-responsive-lg > .table-bordered {
                border: 0;
            }
        }

        @media (max-width: 1199.98px) {
            .table-responsive-xl {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: -ms-autohiding-scrollbar;
            }
            .table-responsive-xl > .table-bordered {
                border: 0;
            }
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
        }

        .table-responsive > .table-bordered {
            border: 0;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
<!-- Define header and footer blocks before your content -->
<header>
    {{ $header }}
</header>

<footer>
    {{ $footer }}
</footer>

<!-- Wrap the content of your PDF inside a main tag -->
<main>
    @foreach($abschnitte as $selectAbschnitt)
    <h2>Praxis-Einsatzplan: {{ $selectAbschnitt->name }}<small> Stand: {{ date("d.m.Y") }}</small></h2>
    <p>Änderungen sind vorbehalten</p>
    <p>{{ $selectAbschnitt->inhalt }}</p>
    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">Nachname</th>
            <th>Vorname</th>
            <th>Unternehmen</th>
            <th>Anrechnung in</th>
            <th>Von</th>
            <th>Bis</th>
            <th>Besuch(e)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($selectKlasse->schulerAtDatum(date("Y/m/d",strtotime($selectAbschnitt->begin)))->get() as $schuler)
            @foreach($schuler->praxisEinsatze($selectAbschnitt) as $einsatz)
        <tr>
            <th>{{ $einsatz->schuler->lastname }}</th>
            <td>{{ $einsatz->schuler->firstname }}</td>
            @if($einsatz->unternehmen != null)
                <td>{{ $einsatz->unternehmen->name }}</td>
            @else
                <td><i>Kein Unternehmen</i></td>
            @endif
            @if($einsatz->module() != null)
                <td>{{ $einsatz->module()->name }}</td>
            @else
                <td><i>Keine Anrechnung</i></td>
            @endif
            <td>{{ date("d.m.Y", strtotime($einsatz->startDate)) }}</td>
            <td>{{ date("d.m.Y", strtotime($einsatz->endDate)) }}</td>
            <td>@foreach($einsatz->besuchstermine as $termin)
            {{ date("d.m.Y", strtotime($termin->startDate)) }},
                @endforeach</td>
        </tr>
        @endforeach
        @endforeach
        </tbody>
    </table><p style = "page-break-after: always;" >
    @endforeach

</main>
