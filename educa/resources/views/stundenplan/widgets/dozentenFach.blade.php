<div class="card mt-2">
    <div class="card-header" data-toggle="collapse" data-target="#dozentenFach_collapse"><b><i class="fas fa-compress-alt"></i> {{ \App\Providers\AppServiceProvider::schoolTranslation('facher', 'Fächer') }}-Verteilung</b>
    </div>
    <div class="collapse show" id="dozentenFach_collapse">
    <table id="dozenten_fach_table" class=" table table-striped table-bordered" style="margin-top: 0px !important;">
        <thead>
        <tr>
            <th>Fach</th>
            <th>Ist</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    </div>
</div>
