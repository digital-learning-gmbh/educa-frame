@extends('layouts.unternehmen')

@section('appContent')
    <div class="container-fluid">
    <div id="visualization">

    </div>
    </div>
@endsection

@section('additionalScript')
    <script type="text/javascript" src="/js/vis/vis-timeline-graph2d.min.js"></script>
    <script>
        var groups = new vis.DataSet([
            {id: 0, content: 'Max Mustermann', value: 1},
            {id: 1, content: 'Tom Test', value: 3},
            {id: 2, content: 'Marie Mustermann', value: 2}
        ]);

        // create a dataset with items
        // note that months are zero-based in the JavaScript Date object, so month 3 is April
        var items = new vis.DataSet([
            {id: 0, group: 0, content: 'Station 1', start: new Date(2014, 3, 17), end: new Date(2014, 3, 21)},
            {id: 1, group: 0, content: 'Praxisbesuch', start: new Date(2014, 3, 19), end: new Date(2014, 3, 20)},
            {id: 2, group: 1, content: 'Station 2', start: new Date(2014, 3, 16), end: new Date(2014, 3, 24)},
            {id: 3, group: 1, content: 'Praxisbesuch', start: new Date(2014, 3, 23), end: new Date(2014, 3, 24)},
            {id: 4, group: 1, content: 'Station 1', start: new Date(2014, 3, 24), end: new Date(2014, 3, 30)},
            {id: 5, group: 2, content: 'Externes Praktikum', start: new Date(2014, 3, 24), end: new Date(2014, 3, 27)}
        ]);

        // create visualization
        var container = document.getElementById('visualization');
        var options = {
            // option groupOrder can be a property name or a sort function
            // the sort function must compare two groups and return a value
            //     > 0 when a > b
            //     < 0 when a < b
            //       0 when a == b
            groupOrder: function (a, b) {
                return a.value - b.value;
            },
            editable: true
        };

        var timeline = new vis.Timeline(container);
        timeline.setOptions(options);
        timeline.setGroups(groups);
        timeline.setItems(items);

    </script>
@endsection
