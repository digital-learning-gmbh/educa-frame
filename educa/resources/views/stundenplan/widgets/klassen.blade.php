<div class="card mt-2">
    <div class="card-header" data-toggle="collapse" data-target="#klasse_collapse"><b><i class="fas fa-compress-alt"></i> {{ \App\Providers\AppServiceProvider::schoolTranslation('klassen', 'Klassen') }}-Verteilung</b>
    </div>
    <div class="collapse show" id="klasse_collapse">
    <table id="klasseverteilung_table" class=" table table-striped table-bordered" style="margin-top: 0px !important;">
        <thead>
        <tr>
            <th>Klasse</th>
            <th>Ist</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    </div>
</div>
