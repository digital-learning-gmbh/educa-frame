<div class="row">
    <div class="col-sm">
        <div style="margin-bottom: 3px;">
            <div class="clearfix"></div>
        </div>
        <table id="table_id" class="tree table table-striped table-bordered">
            <thead>
            <tr>
                <th>Name</th>
                <th>Typ</th>
                <th>Größe</th>
                <th>zuletzt geändert</th>
                <th>Hochgeladen von</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @component('klassenbuch.serienbrief.childern_list',[ "documents" => $model->dokumente(isset($mainCategory) ?  $mainCategory : "haupt")])
            @endcomponent
            </tbody>
        </table>
    </div>
</div>
