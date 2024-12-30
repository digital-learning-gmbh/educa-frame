<table id="report_tabelle_{{ $report->id }}">
    <thead>
    <tr>
        @foreach($report->columns->column as $column)
            <th>{{ $column->name }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                @foreach($report->columns->column as $column)
                <td>{{ $row->{$column->sql} }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
